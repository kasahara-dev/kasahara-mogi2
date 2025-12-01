<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attendance_start_num' => ['lte:attendance_end_num'],
            'rest_start_num.*' => [
                'nullable',
                'lte:attendance_end_num,rest_end_num.*',
                'gte:attendance_start_num'
            ],
            'rest_end_num.*' => [
                'nullable',
                'lte:attendance_end_num'
            ],
            'rest_start_hour.*' => [
                'nullable',
                'required_with:rest_start_minute.*,rest_end_hour.*,rest_end_minute.*',
            ],
            'rest_start_minute.*' => [
                'nullable',
                'required_with:rest_start_hour.*,rest_end_hour.*,rest_end_minute.*',
            ],
            'rest_end_hour.*' => [
                'nullable',
                'required_with:rest_start_hour.*,rest_start_minute.*,rest_end_minute.*',
            ],
            'rest_end_minute.*' => [
                'nullable',
                'required_with:rest_start_hour.*,rest_start_minute.*,rest_end_hour.*',
            ],
            'rest_batting.*' => ['lte:0'],
            'note' => ['required', 'max:255']
        ];
    }
    public function messages()
    {
        return [
            'attendance_start_num.lte' => '出勤時間が不適切な値です',
            'rest_start_num.*.lte' => '休憩時間が不適切な値です',
            'rest_start_num.*.gte' => '休憩時間が不適切な値です',
            'rest_end_num.*.lte' => '休憩時間もしくは退勤時間が不適切な値です',
            'rest_start_hour.*.required_with' => '休憩時間が不適切な値です',
            'rest_start_minute.*.required_with' => '休憩時間が不適切な値です',
            'rest_end_hour.*.required_with' => '休憩時間が不適切な値です',
            'rest_end_minute.*.required_with' => '休憩時間が不適切な値です',
            'rest_batting.*.lte' => '休憩時間が他の休憩時間と重複しています',
            'note.required' => '備考を記入してください',
            'note.max' => '備考は255文字以内で記入してください',
        ];
    }
    protected function prepareForValidation()
    {
        $attendanceStartNum = intval($this->attendance_start_hour) * 100 + intval($this->attendance_start_minute);
        $attendanceEndNum = intval($this->attendance_end_hour) * 100 + intval($this->attendance_end_minute);
        $restStartNum = [];
        $restEndNum = [];
        $restStartHour = [];
        $restStartMinute = [];
        $restEndHour = [];
        $restEndMinute = [];
        $restBatting = [];
        foreach ($this->rest_start_hour as $key => $rest) {
            \Log::info('rest start hour is ' . $key . ' to ' . $rest);
        }
        foreach ($this->rest_start_minute as $key => $rest) {
            \Log::info('rest start minute is ' . $key . ' to ' . $rest);
        }
        foreach ($this->rest_end_hour as $key => $rest) {
            \Log::info('rest end hour is ' . $key . ' to ' . $rest);
        }
        foreach ($this->rest_end_minute as $key => $rest) {
            \Log::info('rest end minute is ' . $key . ' to ' . $rest);
        }

        // 時刻比較用に数値化
        foreach ($this->rest_start_hour as $key => $rest) {
            if (!array_key_exists($key, $this->rest_end_minute)) {
                $editEndMinute = '0';
            } else {
                $editEndMinute = $this->rest_end_minute;
            }
            if (intval($this->rest_start_hour[$key]) < 0 or intval($this->rest_start_minute[$key]) < 0 or intval($this->rest_end_hour[$key]) < 0 or intval($editEndMinute) < 0) {
                $restStartNum[$key] = null;
                $restEndNum[$key] = null;
            } else {
                $restStartNum[$key] = intval($this->rest_start_hour[$key]) * 100 + intval($this->rest_start_minute[$key]);
                $restEndNum[$key] = intval($this->rest_end_hour[$key]) * 100 + intval($editEndMinute);
            }
        }
        // value=-1を''に変換
        foreach ($this->rest_start_hour as $key => $rest) {
            if ($rest < 0) {
                $restStartHour[$key] = '';
            } else {
                $restStartHour[$key] = $rest;
            }
        }
        foreach ($this->rest_start_minute as $key => $rest) {
            if ($rest < 0) {
                $restStartMinute[$key] = '';
            } else {
                $restStartMinute[$key] = $rest;
            }
        }
        foreach ($this->rest_end_hour as $key => $rest) {
            if ($rest < 0) {
                $restEndHour[$key] = '';
            } else {
                $restEndHour[$key] = $rest;
            }
        }
        foreach ($this->rest_end_minute as $key => $rest) {
            if ($rest < 0) {
                $restEndMinute[$key] = '';
            } else {
                $restEndMinute[$key] = $rest;
            }
        }
        // 休憩重複チェック用
        foreach ($restStartNum as $key => $start) {
            $restBatting[$key] = 0;
            for ($i = $key; $i > 1; $i--) {
                if (
                    (!is_null($start) && !is_null($restStartNum[$key - 1])) and (
                        ($start <= $restStartNum[$key - 1] && $restEndNum[$key] > $restStartNum[$key - 1]) or
                        ($start >= $restStartNum[$key - 1] && $restEndNum[$key] <= $restEndNum[$key - 1]) or
                        ($start <= $restEndNum[$key - 1] && $restEndNum[$key] > $restEndNum[$key - 1]))
                ) {
                    $restBatting[$key] += 1;
                }
            }
        }
        $this->merge([
            'rest_start_hour' => $restStartHour,
            'rest_start_minute' => $restStartMinute,
            'rest_end_hour' => $restEndHour,
            'rest_end_minute' => $restEndMinute,
            'attendance_start_num' => $attendanceStartNum,
            'attendance_end_num' => $attendanceEndNum,
            'rest_start_num' => $restStartNum,
            'rest_end_num' => $restEndNum,
            'rest_batting' => $restBatting,
        ]);
    }
}
