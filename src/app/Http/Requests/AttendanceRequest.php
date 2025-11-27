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
            'rest_start_hour.*' => [
                // 'required',
                'nullable',
                'required_with:rest_start_minute.*,attendance_end_hour.*,attendance_end_minute.*',
            ]
        ];
    }
    public function messages()
    {
        return [
            'attendance_start_num.lte' => '出勤時間が不適切な値です',
            'rest_start_hour.*.required' => '休憩時間が不適切な値です',
            'rest_start_hour.*.required_with' => '休憩時間が不適切な値です',
        ];
    }
    protected function prepareForValidation()
    {
        $attendanceStartNum = intval($this->attendance_start_hour) * 100 + intval($this->attendance_start_minute);
        $attendanceEndNum = intval($this->attendance_end_hour) * 100 + intval($this->attendance_end_minute);
        $this->merge([
            'attendance_start_num' => $attendanceStartNum,
            'attendance_end_num' => $attendanceEndNum

        ]);
    }

}
