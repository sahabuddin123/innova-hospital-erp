<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Schedule_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getSchedule()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('time_schedule');
        return $query->result();
    }

    function getAvailableDoctorByDate($date)
    {

        $weekday = date("l", $date);
        $this->db->where('date', $date);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query1 = $this->db->get('holidays')->result();
        if (!empty($query1)) {
            $doctor = array();
            foreach ($query1 as $q1) {
                $doctor[] = $q1->doctor;
            }
            $this->db->where_not_in('id', $staff);
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query = $this->db->get('doctor')->result();
        foreach ($query as $availableDoctor) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('doctor', $availableDoctor->id);
            $this->db->where('weekday', $weekday);
            $query_slot = $this->db->get('time_slot')->result();

            if (!empty($query_slot)) {
                $doctor_avail[] = $availableDoctor->id;
            }
        }
        $this->db->where_in('id', $doctor_avail);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query_avail_doctor = $this->db->get('doctor');
        return $query_avail_doctor->result();
    }

    function getAvailableDoctorsByDateBySlot($date, $slot)
    {

        $weekday = date("l", $date);
        $this->db->where('date', $date);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query1 = $this->db->get('holidays')->result();
        if (!empty($query1)) {
            $doctor = array();
            foreach ($query1 as $q1) {
                $doctor[] = $q1->doctor;
            }
            $this->db->where_not_in('id', $doctor);
        }

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', 1);
        $query = $this->db->get('doctor')->result();
        foreach ($query as $availableDoctor) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $this->db->where('doctor', $availableDoctor->id);
            $this->db->where('weekday', $weekday);
            $query_slot = $this->db->get('time_slot')->result();

            if (!empty($query_slot)) {
                $doctor_avail[] = $availableDoctor->id;
            }
        }

        foreach ($doctor_avail as $key => $value) {
            $this->db->where('doctor', $value);
            $this->db->where('date', $date);
            $this->db->where('time_slot', $slot);
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $query_appointment = $this->db->get('appointment')->result();

            if (empty($query_appointment)) {
                $most_probable_avail_doctor[] = $value;
            }
        }
        $this->db->where_in('id', $most_probable_avail_doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query_avail_doctor = $this->db->get('staff');
        return $query_avail_doctor->result();
    }

    function getAvailableSlotByDoctorByDate($date, $doctor)
    {
        $weekday = date("l", $date);
        $target_day_str = date("Y-m-d", $date);
        $target_timestamp = strtotime($target_day_str);

        // Check if doctor is active, available, and has configured schedules
        $this->load->model('doctor/doctor_model');
        $doctor_info = $this->doctor_model->getDoctorById($doctor);
        if (empty($doctor_info) || $doctor_info->active != 1 || (isset($doctor_info->available) && $doctor_info->available == '0')) {
            return array();
        }
        if (!empty($doctor_info->schedule_flag)) {
            return array();
        }

        // Check holidays and leave records for doctor
        $this->db->where('doctor', $doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $holidays = $this->db->get('holidays')->result();

        $is_full_day_off = false;
        $partial_leaves = array();

        foreach ($holidays as $h) {
            $h_start_day = is_numeric($h->date) ? date("Y-m-d", (int)$h->date) : date("Y-m-d", strtotime($h->date));
            $h_start_ts = strtotime($h_start_day);

            if (!empty($h->end_date)) {
                $h_end_day = is_numeric($h->end_date) ? date("Y-m-d", (int)$h->end_date) : date("Y-m-d", strtotime($h->end_date));
                $h_end_ts = strtotime($h_end_day);
            } else {
                $h_end_ts = $h_start_ts;
            }

            if ($target_timestamp >= $h_start_ts && $target_timestamp <= $h_end_ts) {
                if (empty($h->leave_type) || $h->leave_type == 'full_day' || $h->leave_type == 'emergency') {
                    $is_full_day_off = true;
                    break;
                } elseif ($h->leave_type == 'partial_day') {
                    $partial_leaves[] = array(
                        'start' => !empty($h->start_time) ? strtotime($target_day_str . ' ' . $h->start_time) : null,
                        'end' => !empty($h->end_time) ? strtotime($target_day_str . ' ' . $h->end_time) : null,
                    );
                }
            }
        }

        if ($is_full_day_off) {
            return array();
        }

        $this->db->where('date', $date);
        $this->db->where('doctor', $doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $this->db->order_by('s_time_key', 'asc');
        $query1 = $this->db->get('time_slot')->result();

        $availabletimeSlot = array();
        $bookedTimeSlot = array();

        $this->db->where('hospital_id', $this->hospital_id);
        $this->settings = $this->db->get('settings')->row();

        foreach ($query1 as $timeslot) {
            // Check if slot falls in partial day leave
            if (!empty($partial_leaves)) {
                $slot_start_ts = strtotime($target_day_str . ' ' . $timeslot->s_time);
                $slot_end_ts = strtotime($target_day_str . ' ' . $timeslot->e_time);
                $blocked_by_partial = false;
                foreach ($partial_leaves as $pl) {
                    if ($pl['start'] && $pl['end']) {
                        if ($slot_start_ts < $pl['end'] && $slot_end_ts > $pl['start']) {
                            $blocked_by_partial = true;
                            break;
                        }
                    }
                }
                if ($blocked_by_partial) {
                    continue;
                }
            }

            if ($this->settings->time_format == 24) {
                $timeslot->s_time = $this->settings_model->convert_to_24h($timeslot->s_time);
                $timeslot->e_time = $this->settings_model->convert_to_24h($timeslot->e_time);
            }
            $availabletimeSlot[] = $timeslot->s_time . ' To ' . $timeslot->e_time;
        }

        foreach ($query as $bookedTime) {
            if ($bookedTime->status != 'Cancelled') {
                if ($this->settings->time_format == 24) {
                    $time_string = explode(' To ', $bookedTime->time_slot);
                    $time_string[0] = $this->settings_model->convert_to_24h($time_string[0]);
                    $time_string[1] = $this->settings_model->convert_to_24h($time_string[1]);
                } else {
                    $time_string = explode(' To ', $bookedTime->time_slot);
                }

                $bookedTimeSlot[] = $time_string[0] . ' To ' . $time_string[1];
            }
        }

        $availableSlot = array_diff($availabletimeSlot, $bookedTimeSlot);
        return array_values($availableSlot);
    }

    function getAvailableSlotByDoctorByDateByAppointmentId($date, $doctor, $appointment_id)
    {
        $weekday = date("l", $date);
        $target_day_str = date("Y-m-d", $date);
        $target_timestamp = strtotime($target_day_str);

        // Check if doctor is active, available, and has configured schedules
        $this->load->model('doctor/doctor_model');
        $doctor_info = $this->doctor_model->getDoctorById($doctor);
        if (empty($doctor_info) || $doctor_info->active != 1 || (isset($doctor_info->available) && $doctor_info->available == '0')) {
            return array();
        }
        if (!empty($doctor_info->schedule_flag)) {
            return array();
        }

        // Check holidays and leave records for doctor
        $this->db->where('doctor', $doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $holidays = $this->db->get('holidays')->result();

        $is_full_day_off = false;
        $partial_leaves = array();

        foreach ($holidays as $h) {
            $h_start_day = is_numeric($h->date) ? date("Y-m-d", (int)$h->date) : date("Y-m-d", strtotime($h->date));
            $h_start_ts = strtotime($h_start_day);

            if (!empty($h->end_date)) {
                $h_end_day = is_numeric($h->end_date) ? date("Y-m-d", (int)$h->end_date) : date("Y-m-d", strtotime($h->end_date));
                $h_end_ts = strtotime($h_end_day);
            } else {
                $h_end_ts = $h_start_ts;
            }

            if ($target_timestamp >= $h_start_ts && $target_timestamp <= $h_end_ts) {
                if (empty($h->leave_type) || $h->leave_type == 'full_day' || $h->leave_type == 'emergency') {
                    $is_full_day_off = true;
                    break;
                } elseif ($h->leave_type == 'partial_day') {
                    $partial_leaves[] = array(
                        'start' => !empty($h->start_time) ? strtotime($target_day_str . ' ' . $h->start_time) : null,
                        'end' => !empty($h->end_time) ? strtotime($target_day_str . ' ' . $h->end_time) : null,
                    );
                }
            }
        }

        if ($is_full_day_off) {
            return array();
        }

        $this->db->where('date', $date);
        $this->db->where('doctor', $doctor);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('appointment')->result();

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $this->db->order_by('s_time_key', 'asc');
        $query1 = $this->db->get('time_slot')->result();

        $availabletimeSlot = array();
        $bookedTimeSlot = array();

        $this->db->where('hospital_id', $this->hospital_id);
        $this->settings = $this->db->get('settings')->row();

        foreach ($query1 as $timeslot) {
            // Check partial day leave conflict
            if (!empty($partial_leaves)) {
                $slot_start_ts = strtotime($target_day_str . ' ' . $timeslot->s_time);
                $slot_end_ts = strtotime($target_day_str . ' ' . $timeslot->e_time);
                $blocked_by_partial = false;
                foreach ($partial_leaves as $pl) {
                    if ($pl['start'] && $pl['end']) {
                        if ($slot_start_ts < $pl['end'] && $slot_end_ts > $pl['start']) {
                            $blocked_by_partial = true;
                            break;
                        }
                    }
                }
                if ($blocked_by_partial) {
                    continue;
                }
            }

            if ($this->settings->time_format == 24) {
                $timeslot->s_time = $this->settings_model->convert_to_24h($timeslot->s_time);
                $timeslot->e_time = $this->settings_model->convert_to_24h($timeslot->e_time);
            }
            $availabletimeSlot[] = $timeslot->s_time . ' To ' . $timeslot->e_time;
        }

        foreach ($query as $bookedTime) {
            if ($bookedTime->status != 'Cancelled') {
                if ($bookedTime->id != $appointment_id) {
                    if ($this->settings->time_format == 24) {
                        $time_string = explode(' To ', $bookedTime->time_slot);
                        $time_string[0] = $this->settings_model->convert_to_24h($time_string[0]);
                        $time_string[1] = $this->settings_model->convert_to_24h($time_string[1]);
                        $bookedTime->time_slot = $time_string[0] . ' ' . lang('to') . ' ' . $time_string[1];
                    } else {
                        $time_string = explode(' To ', $bookedTime->time_slot);
                    }
                    $bookedTimeSlot[] = $time_string[0] . ' To ' . $time_string[1];
                }
            }
        }

        $availableSlot = array_diff($availabletimeSlot, $bookedTimeSlot);
        return array_values($availableSlot);
    }

    function updateIonUser($username, $email, $password, $ion_user_id)
    {
        $uptade_ion_user = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $this->db->where('id', $ion_user_id);
        $this->db->update('users', $uptade_ion_user);
    }

    function getDoctorByIonUserId($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('ion_user_id', $id);
        $query = $this->db->get('doctor');
        return $query->row();
    }

    function insertTimeSlot($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('time_slot', $data2);
    }

    function getTimeSlot()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('time_slot');
        return $query->result();
    }

    function getTimeSlotById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('time_slot');
        return $query->row();
    }

    function getTimeSlotByDoctor($id)
    {
        $this->db->order_by('s_time_key', 'asc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $id);
        $query = $this->db->get('time_slot');
        return $query->result();
    }

    function updateTimeSlot($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('time_slot', $data);
    }

    function deleteTimeSlot($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('time_slot');
    }

    function insertSchedule($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('time_schedule', $data2);
    }

    function getScheduleByDoctor($doctor)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $query = $this->db->get('time_schedule');
        return $query->result();
    }

    function getScheduleById($id)
    {
        $this->db->where('id', $id);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('time_schedule');
        return $query->row();
    }

    function getScheduleByDoctorByWeekday($doctor, $weekday)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $query = $this->db->get('time_schedule');
        return $query->result();
    }

    function getScheduleByDoctorByWeekdayById($doctor, $weekday, $id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where_not_in('id', $id);
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $query = $this->db->get('time_schedule');
        return $query->result();
    }

    function updateSchedule($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('time_schedule', $data);
    }

    function deleteSchedule($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('time_schedule');
    }

    function deleteTimeSlotByDoctorByWeekday($doctor, $weekday)
    {
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $this->db->delete('time_slot');
    }

    function insertHoliday($data)
    {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('holidays', $data2);
    }

    function getHolidays()
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('holidays');
        return $query->result();
    }

    function getHolidayById($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('holidays');
        return $query->row();
    }

    function getHolidaysByDoctor($id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'asc');
        $this->db->where('doctor', $id);
        $query = $this->db->get('holidays');
        return $query->result();
    }

    function getHolidayByDoctorByDate($doctor, $date)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('date', $date);
        $query = $this->db->get('holidays');
        return $query->row();
    }

    function getTimeSlotByDoctorByWeekday($doctor, $weekday)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $query = $this->db->get('time_slot');
        return $query->result();
    }

    function getTimeSlotByDoctorByWeekdayById($doctor, $weekday, $id)
    {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where_not_in('id', $id);
        $this->db->where('doctor', $doctor);
        $this->db->where('weekday', $weekday);
        $query = $this->db->get('time_slot');
        return $query->result();
    }

    function updateHoliday($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('holidays', $data);
    }

    function deleteHoliday($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('holidays');
    }
}
