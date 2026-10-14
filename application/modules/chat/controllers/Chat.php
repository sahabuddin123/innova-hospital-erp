<?php

Class Chat extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('chat_model'); 
    }
    
    public function index() {
        
        $users = $this->db->get_where('users_groups', array('group_id' => 11))->result_array();
        $admins = array();
        foreach($users as $user) {
            $result = $this->db->get_where('users', array('id' => $user['user_id']))->row();
            if($result) {

                $hospital = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row();
               
            }
        }
        $current_user = $this->ion_auth->user()->row();
        $id = !empty($current_user->id) ? $current_user->id : 0;
        $data['current_user'] = $id;

        $hospital_id = $this->session->userdata('hospital_id');
        $hospital = !empty($hospital_id) ? $this->db->get_where('hospital', array('id' => $hospital_id))->row() : null;
        $data['admins'] = (!empty($hospital) && !empty($hospital->ion_user_id)) ? $this->db->get_where('users', array('id' => $hospital->ion_user_id))->result_array() : array();
            
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array1 = $this->db->get('accountant')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array2 = $this->db->get('laboratorist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array3 = $this->db->get('receptionist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array4 = $this->db->get('pharmacist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array5 = $this->db->get('nurse')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array6 = $this->db->get('doctor')->result_array();
            
        $alreadyDone = array();
        for($i = 0; $i < count($data['admins']); $i++) {
            $data['admins'][$i]['newChat'] = '';
            if($id != $data['admins'][$i]['id']) {
                $this->db->limit(1);
                $this->db->order_by('date_time', 'desc');
                $this->db->where('sender_id', $data['admins'][$i]['id']);
                $this->db->where('receiver_id', $id);
                $latestChat = $this->db->get('chat')->row(); 
                if(!empty($latestChat) && !empty($latestChat->status) && $latestChat->status == 'unread') {
                    $data['admins'][$i]['newChat'] = 'unread';
                } else {
                    $data['admins'][$i]['newChat'] = 'read';
                }
                array_push($alreadyDone, $data['admins'][$i]['id']);
            }
        }
        
        $data['employees'] = array_merge( $array1, $array2, $array3, $array4, $array5, $array6);
        $this->db->limit(1);
        $this->db->order_by('date_time', 'desc');
        $this->db->where('sender_id', $id);
        $this->db->or_where('receiver_id', $id);
        $chat = $this->db->get('chat')->row(); 
        $data['chats'] = '';
        $data['receiver_id'] = '';
        
        for($i = 0; $i < count($data['employees']); $i++) {
            $data['employees'][$i]['newChat'] = '';
            if(!empty($data['employees'][$i]['ion_user_id']) && $id != $data['employees'][$i]['ion_user_id']) {
                    $this->db->limit(1);
                    $this->db->order_by('date_time', 'desc');
                    $this->db->where('sender_id', $data['employees'][$i]['ion_user_id']);
                    $this->db->where('receiver_id', $id);
                    $latestChat = $this->db->get('chat')->row();
                    if(!empty($latestChat)){
                        if(!empty($latestChat->status) && $latestChat->status == 'unread') {
                            $data['employees'][$i]['newChat'] = 'unread';
                        } else {
                            $data['employees'][$i]['newChat'] = 'read';
                        }
                    } else {
                        $data['employees'][$i]['newChat'] = '';
                    }
            }
        }
        $this->db->distinct();
        $this->db->order_by('date_time','desc');
        $this->db->group_start();
        $this->db->where('sender_id', $id);
        $this->db->or_where('receiver_id', $id);
        $this->db->group_end();
        $latestChatsResults = $this->db->get('chat')->result();
        $data['employeeChat'] = array();
        
        $employeeCount = 0;
        foreach($latestChatsResults as $latestChat) {
            $chatUserId = $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id;
            if(!in_array($chatUserId, $alreadyDone)) {
                        $data['employeeChat'][$employeeCount]['ion_user_id'] = $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id;
                        $data['employeeChat'][$employeeCount]['newChat'] = $latestChat->status == 'read' ? 'read' : 'unread';
                        $data['employeeChat'][$employeeCount]['name'] = $latestChat->sender_id == $id ? $this->chat_model->getName($latestChat->receiver_id) : $this->chat_model->getName($latestChat->sender_id);
                        $employeeCount++;
                        array_push($alreadyDone, $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id);
            }
        }

        for($i = 0; $i < count($data['employees']); $i++) {
            if($id != $data['employees'][$i]['ion_user_id'] && !in_array($data['employees'][$i]['ion_user_id'], $alreadyDone)) {
                    $this->db->limit(1);
                    $this->db->order_by('date_time', 'desc');
                    $this->db->where('sender_id', $data['employees'][$i]['ion_user_id']);
                    $this->db->where('receiver_id', $id);
                    $latestChat = $this->db->get('chat')->row();
                    if(!empty($latestChat)){
                        if($latestChat->status == 'unread') {
                            $data['employeeChat'][$employeeCount]['ion_user_id'] = $data['employees'][$i]['ion_user_id'];
                            $data['employeeChat'][$employeeCount]['newChat'] = 'unread';
                            $data['employeeChat'][$employeeCount]['name'] = $data['employees'][$i]['name'];

                            //$data['employees'][$i]['newChat'] .=  'unread';
                        } else {
                            //$data['employees'][$i]['newChat'] .=  'read';

                            $data['employeeChat'][$employeeCount]['ion_user_id'] = $data['employees'][$i]['ion_user_id'];
                            $data['employeeChat'][$employeeCount]['newChat'] =  'read';
                            $data['employeeChat'][$employeeCount]['name'] = $data['employees'][$i]['name'];

                        }
                    } else {
                        $data['employeeChat'][$employeeCount]['ion_user_id'] = $data['employees'][$i]['ion_user_id'];
                        $data['employeeChat'][$employeeCount]['newChat'] =  'read';
                        $data['employeeChat'][$employeeCount]['name'] = $data['employees'][$i]['name'];
                    }
                    $employeeCount++;
            }
        }
        
        
        
        
        
       if($chat) {
            $id2 = '';
            if($chat->sender_id == $id) {
                $id2 = $chat->receiver_id;
            } else if($chat->receiver_id == $id) {
                $id2 = $chat->sender_id;
            }
            
            $user_row = !empty($id2) ? $this->db->get_where('users', array('id' => $id2))->row() : null;
            $data['user'] = !empty($user_row->username) ? $user_row->username : '';
            
            $this->db->limit(20);
            $this->db->order_by('date_time', 'desc');
            $this->db->group_start();
            $this->db->where('sender_id', $id);
            $this->db->where('receiver_id', $id2);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('sender_id', $id2);
            $this->db->where('receiver_id', $id);
            $this->db->group_end();
            $chats = $this->db->get('chat')->result_array();

            for($i=(count($chats)-1); $i >= 0; $i--) {
                if($chats[$i]['sender_id'] == $id) {
                    $status = array(
                        'status' => 'read'
                    );
                    $this->db->where('id', $chats[$i]['id']);
                    $this->db->update('chat', $status);
                    $data['chats'] .= '<span class="chat-sender">'.$chats[$i]['chat_text'].'</span>';
                } else {
                    $status = array(
                        'status' => 'read'
                    );
                    $this->db->where('id', $chats[$i]['id']);
                    $this->db->update('chat', $status);
                    $data['chats'] .= '<span class="chat-receiver">'.$chats[$i]['chat_text'].'</span>';
                }
            }
            
            $data['lastMessageId'] = !empty($chats) ? $chats[count($chats)-1]['id'] : 0;
            $data['recentMessageId'] = !empty($chats) ? $chats[0]['id'] : 0;

            $data['receiver_id'] = $id2;
            
            $employee = array();
       } else {
           if(!empty($data['employees'])){
           $data['lastMessageId'] = 0;
           $data['recentMessageId'] = 0;
           $first_emp_ion_id = !empty($data['employees'][0]['ion_user_id']) ? $data['employees'][0]['ion_user_id'] : 0;
           $data['receiver_id'] = $first_emp_ion_id;
           $emp_user = !empty($first_emp_ion_id) ? $this->db->get_where('users', array('id' => $first_emp_ion_id))->row() : null;
           $data['user'] = !empty($emp_user->username) ? $emp_user->username : ' ';
       }else{
            $data['lastMessageId'] = 0;
           $data['recentMessageId'] = 0;
           $data['receiver_id'] = ' ';
           $data['user'] = ' ';
       }
       }
        
        
        $this->load->view('home/dashboard');
        $this->load->view('chat', $data);
        $this->load->view('home/footer');
    }
    
    public function sendChat() {
        $chat = $this->input->get('chat');
        $receiver_id = $this->input->get('receiverId');
        $id = $this->ion_auth->user()->row()->id;
        $data = array(
            'sender_id' => $id,
            'receiver_id' => $receiver_id,
            'date_time' => date('Y-m-d H:i:s'),
            'status' => 'unread',
            'chat_text' => $chat,
        );
        $this->db->insert('chat', $data);
        echo json_encode('success');
    }
    
    public function checkChat() {
        $id = $this->ion_auth->user()->row()->id;
        $receiverId = $this->input->get('receiverId');
        $recentId = $this->input->get('recentId');
        
        $data['currentChats'] = '';
        $data['recentId'] = ''; 
        
        $this->db->order_by('date_time', 'desc');
        $this->db->where('id >', $recentId);
        $this->db->where('receiver_id', $id);
        $this->db->where('sender_id', $receiverId);
        $chats = $this->db->get('chat')->result_array();
        
//        $data['chats'] = '';
        for($i=(count($chats)-1); $i >= 0; $i--) {
                if($chats[$i]['sender_id'] == $id) {
                    $data['currentChats'] .= '<span class="chat-sender">'.$chats[$i]['chat_text'].'</span>';
                } else {
                    $data['currentChats'] .= '<span class="chat-receiver">'.$chats[$i]['chat_text'].'</span>';
                }
                
                $data2 = array(
                    'status' => 'read'
                );
                $this->db->where('id', $chats[$i]['id']);
                $this->db->update('chat', $data2);
         }
//        
         if(count($chats) > 0) {
             $data['recentId'] = $chats[0]['id'];
         } else {
             $data['recentId'] = $recentId;
         }
         
            $hospital = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row();
            $admins = $this->db->get_where('users', array('id' => $hospital->ion_user_id))->result_array();
            
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array1 = $this->db->get('accountant')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array2 = $this->db->get('laboratorist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array3 = $this->db->get('receptionist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array4 = $this->db->get('pharmacist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array5 = $this->db->get('nurse')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array6 = $this->db->get('doctor')->result_array();
            $employees = array_merge( $array1, $array2, $array3, $array4, $array5, $array6);
            
            $data['unreads'] = array();
            $data['html'] = '<p style="margin-top: 1.5rem; font-weight: bolder;">'.lang('admin').'</p><div id="adminChatters">';
                        $alreadyDone = array();
           for($i = 0; $i < count($admins); $i++) {
                if($id != $admins[$i]['id']) {
                    $this->db->limit(1);
                    $this->db->order_by('date_time', 'desc');
                    $this->db->where('sender_id', $admins[$i]['id']);
                    $this->db->where('receiver_id', $id);
                    $this->db->where('status', 'unread');
                    $unreadChat = $this->db->get('chat')->row();
                    if(empty($unreadChat)) {
                        if($receiverId == $admins[$i]['id']) {
                            $data['html'] .= '<button class="ca-btn ca-chat-btn d-block ca-selected-chat" data-id="'.$admins[$i]['id'].'">'.$admins[$i]['username'].'</button>';
                        } else {
                            $data['html'] .= '<button class="ca-btn ca-chat-btn d-block" data-id="'.$admins[$i]['id'].'">'.$admins[$i]['username'].'</button>';
                        }
                        
                    } else {
                           $data['html'] .= '<button class="ca-btn ca-chat-btn d-block newChat" data-id="'.$admins[$i]['id'].'">'.$admins[$i]['username'].'</button>';
                    }
                    
                    array_push($alreadyDone, $admins[$i]['id']);
                 }
            }
            $data['html'] .= '</div><p style="margin-top: 1.5rem; font-weight: bolder;">Employees</p><div id="employeeChatters">';
            
            $this->db->distinct();
            $this->db->order_by('date_time','desc');
            $this->db->group_start();
            $this->db->where('sender_id', $id);
            $this->db->or_where('receiver_id', $id);
            $this->db->group_end();
            $latestChatsResults = $this->db->get('chat')->result();
            $data['employeeChat'] = array();
            $employeeCount = 0;
            foreach($latestChatsResults as $latestChat) {
                $chatUserId = $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id;
                if(!in_array($chatUserId, $alreadyDone)) {
                            $data['employeeChat'][$employeeCount]['ion_user_id'] = $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id;
                            $data['employeeChat'][$employeeCount]['newChat'] = ($latestChat->status == 'unread' && $latestChat->receiver_id == $id) ? 'unread' : 'read';
                            $data['employeeChat'][$employeeCount]['name'] = $latestChat->sender_id == $id ? $this->chat_model->getName($latestChat->receiver_id) : $this->chat_model->getName($latestChat->sender_id);
                            
                             if($data['employeeChat'][$employeeCount]['ion_user_id'] == $receiverId ) {
                                    $data['html'] .= '<button class="ca-btn ca-chat-btn d-block ca-selected-chat" data-id="'.$data['employeeChat'][$employeeCount]['ion_user_id'].'">'.$data['employeeChat'][$employeeCount]['name'].'</button>';
                             } else if($data['employeeChat'][$employeeCount]['newChat'] == 'unread' ) {
                                $data['html'] .= '<button class="ca-btn ca-chat-btn d-block newChat" data-id="'.$data['employeeChat'][$employeeCount]['ion_user_id'].'">'.$data['employeeChat'][$employeeCount]['name'].'</button>';
                            } else {
                                $data['html'] .= '<button class="ca-btn ca-chat-btn d-block" data-id="'.$data['employeeChat'][$employeeCount]['ion_user_id'].'">'.$data['employeeChat'][$employeeCount]['name'].'</button>';
                            }
                            
                            $employeeCount++;
                            array_push($alreadyDone, $latestChat->sender_id == $id ? $latestChat->receiver_id : $latestChat->sender_id);
                }
            }

            for($i = 0; $i < count($employees); $i++) {
                if($id != $employees[$i]['ion_user_id'] && !in_array($employees[$i]['ion_user_id'], $alreadyDone)) {
//                        $this->db->limit(1);
//                        $this->db->order_by('date_time', 'desc');
//                        $this->db->where('sender_id', $data['employees'][$i]['ion_user_id']);
//                        $this->db->where('receiver_id', $id);
//                        $latestChat = $this->db->get('chat')->row();
                        $data['html'] .= '<button class="ca-btn ca-chat-btn d-block" data-id="'.$employees[$i]['ion_user_id'].'">'.$employees[$i]['name'].'</button>';
                        $employeeCount++;
                }
            }
            
            $data['html'] .= '</div>';  
            
//            for($i = 0; $i < count($employees); $i++) {
//                if($id != $employees[$i]['ion_user_id']) {
//                    $this->db->limit(1);
//                    $this->db->order_by('date_time', 'desc');
//                    $this->db->where('sender_id', $employees[$i]['ion_user_id']);
//                    $this->db->where('receiver_id', $id);
//                    $this->db->where('status', 'unread');
//                    $unreadChat = $this->db->get('chat')->row();
//                    if(empty($unreadChat)) {
//
//                    } else {
//                        array_push($data['unreads'], $employees[$i]['ion_user_id']);
//                    }
//               }
//            }
        
//        $data['sender_ids'] = array();
//        for($i = count($chats)-1; $i >= 0; $i--) {
//            array_push($data['sender_ids'], $chats[$i]->sender_id); 
//        }
        
        echo json_encode($data);
    }
    
    
    
    public function checkChat2() {
        $user = $this->ion_auth->user()->row();
        $id = !empty($user->id) ? $user->id : 0;
        $hospital_id = $this->session->userdata('hospital_id');
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
         
        $hospital = !empty($hospital_id) ? $this->db->get_where('hospital', array('id' => $hospital_id))->row() : null;
        $admins = (!empty($hospital) && !empty($hospital->ion_user_id)) ? $this->db->get_where('users', array('id' => $hospital->ion_user_id))->result_array() : array();
        
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array1 = $this->db->get('accountant')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array2 = $this->db->get('laboratorist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array3 = $this->db->get('receptionist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array4 = $this->db->get('pharmacist')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array5 = $this->db->get('nurse')->result_array();
        $this->db->select('*');
        $this->db->where('hospital_id', $hospital_id);
        $array6 = $this->db->get('doctor')->result_array();
        $employees = array_merge( $array1, $array2, $array3, $array4, $array5, $array6);
            
            $data['unreads'] = array();
            
            for($i = 0; $i < count($employees); $i++) {
            if($id != $employees[$i]['ion_user_id']) {
                $this->db->limit(1);
                $this->db->order_by('date_time', 'desc');
                $this->db->where('sender_id', $employees[$i]['ion_user_id']);
                $this->db->where('receiver_id', $id);
                $this->db->where('status', 'unread');
                $unreadChat = $this->db->get('chat')->row();
                if(empty($unreadChat)) {
                    
                } else {
                    array_push($data['unreads'], $employees[$i]['ion_user_id']);
                }
           }
        }
        
        for($i = 0; $i < count($admins); $i++) {
            if($id != $admins[$i]['id']) {
                $this->db->limit(1);
                $this->db->order_by('date_time', 'desc');
                $this->db->where('sender_id', $admins[$i]['id']);
                $this->db->where('receiver_id', $id);
                $this->db->where('status', 'unread');
                $unreadChat = $this->db->get('chat')->row();
                if(empty($unreadChat)) {

                } else {
                    array_push($data['unreads'], $admins[$i]['id']);
                }
             }
        }

        
        echo json_encode($data);
    }
    
    public function getOldMessage() {
        $id = $this->ion_auth->user()->row()->id;
        $id2 = $this->input->get('receiverId');
        $lastMessageId = $this->input->get('lastMessageId');
        
            $this->db->limit(20);
            $this->db->order_by('date_time', 'desc');
            $this->db->group_start();
            $this->db->where('id <', $lastMessageId);
            $this->db->where('sender_id', $id);
            $this->db->where('receiver_id', $id2);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('id <', $lastMessageId);
            $this->db->where('sender_id', $id2);
            $this->db->where('receiver_id', $id);
            $this->db->group_end();
            $chats = $this->db->get('chat')->result_array();
            $data['chats'] = '';
            
             if($chats) {
                 for($i=(count($chats)-1); $i >= 0; $i--) {
                if($chats[$i]['sender_id'] == $id) {
                    $data['chats'] .= '<span class="chat-sender">'.$chats[$i]['chat_text'].'</span>';
                } else {
                    $data['chats'] .= '<span class="chat-receiver">'.$chats[$i]['chat_text'].'</span>';
                }
                
                $status = array(
                        'status' => 'read'
                    );
                    $this->db->where('id', $chats[$i]['id']);
                    $this->db->update('chat', $status);
            }
            
                $data['lastMessageId'] = $chats[count($chats)-1]['id'];
             } else {
                 $data['lastMessageId'] = 0;
             }
            
            echo json_encode($data);
    }
    
    public function changeChat() {
        $id2 = $this->input->get('id');
        $id = $this->ion_auth->user()->row()->id;
        
        $data['user'] = $this->db->get_where('users', array('id' => $id2))->row()->username;
        
        $this->db->limit(20);
            $this->db->order_by('date_time', 'desc');
            $this->db->group_start();
            $this->db->where('sender_id', $id);
            $this->db->where('receiver_id', $id2);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('sender_id', $id2);
            $this->db->where('receiver_id', $id);
            $this->db->group_end();
            $chats = $this->db->get('chat')->result_array();

            if($chats) {
                 for($i=(count($chats)-1); $i >= 0; $i--) {
                if($chats[$i]['sender_id'] == $id) {
                    $status = array(
                        'status' => 'read'
                    );
                    $this->db->where('id', $chats[$i]['id']);
                    $this->db->update('chat', $status);
                    $data['chats'] .= '<span class="chat-sender">'.$chats[$i]['chat_text'].'</span>';
                } else {
                    $status = array(
                        'status' => 'read'
                    );
                    $this->db->where('id', $chats[$i]['id']);
                    $this->db->update('chat', $status);
                    $data['chats'] .= '<span class="chat-receiver">'.$chats[$i]['chat_text'].'</span>';
                }
            }
            
            $data['lastMessageId'] = $chats[count($chats)-1]['id'];
            $data['recentId'] = $chats[0]['id'];
             } else {
                 $data['lastMessageId'] = 0;
                 $data['recentId'] = 0;
             }
            
            echo json_encode($data);
    }
    
    public function findChatPerson() {
        $id = $this->ion_auth->user()->row()->id;
        $search = $this->input->get('search');
        
        if($search == '') {
            $hospital = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row();
        
        $this->db->where('id', $hospital->ion_user_id);
        $admins = $this->db->get('users')->result_array();
        //$data['admins'] = $this->db->get_where('users', array('id' => $hospital->ion_user_id))->result_array();
            
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array1 = $this->db->get('accountant')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array2 = $this->db->get('laboratorist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array3 = $this->db->get('receptionist')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array4 = $this->db->get('pharmacist')->result_array();
        $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array5 = $this->db->get('nurse')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array6 = $this->db->get('doctor')->result_array();
            $employees = array_merge( $array1, $array2, $array3, $array4, $array5, $array6);
    } else {
        $hospital = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row();
        $this->db->group_start();
        $this->db->like('username', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
        $this->db->where('id', $hospital->ion_user_id);
        $admins = $this->db->get('users')->result_array();
        
            
            $this->db->select('*');
            $this->db->group_start();
        $this->db->like('name', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array1 = $this->db->get('accountant')->result_array();
            $this->db->select('*');
            $this->db->group_start();
        $this->db->like('name', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array2 = $this->db->get('laboratorist')->result_array();
            $this->db->select('*');
            $this->db->group_start();
        $this->db->like('name', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array3 = $this->db->get('receptionist')->result_array();
            $this->db->select('*');
            $this->db->group_start();
        $this->db->like('name', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array4 = $this->db->get('pharmacist')->result_array();
        $this->db->select('*');
        $this->db->group_start();
        $this->db->like('name', $search);
        $this->db->or_like('email', $search);
        $this->db->group_end();
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array5 = $this->db->get('nurse')->result_array();
            $this->db->select('*');
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
            $array6 = $this->db->get('doctor')->result_array();
            $employees = array_merge( $array1, $array2, $array3, $array4, $array5, $array6);
    }
        
        
        
        $data['admin'] = '';
        $data['employee'] = '';
        
        
        for($i = 0; $i < count($employees); $i++) {
            if($id != $employees[$i]['ion_user_id']) {
                $this->db->limit(1);
                $this->db->order_by('date_time', 'desc');
                $this->db->where('sender_id', $employees[$i]['ion_user_id']);
                $this->db->where('receiver_id', $id);
                $this->db->where('status', 'unread');
                $unreadChat = $this->db->get('chat')->row();
                if(empty($unreadChat)) {
                    $unreadChat = '';
                } else {
                    $unreadChat = 'newChat';
                }
                
                $data['employee'] .=     '<button class="ca-btn ca-chat-btn d-block '.$unreadChat.'" data-id="'.$employees[$i]['ion_user_id'].'">'.$employees[$i]['name'].'</button>';
            }
        }
        
        for($i = 0; $i < count($admins); $i++) {
            if($id != $admins[$i]['id']) {
                $this->db->limit(1);
                $this->db->order_by('date_time', 'desc');
                $this->db->where('sender_id', $admins[$i]['id']);
                $this->db->where('receiver_id', $id);
                $this->db->where('status', 'unread');
                $unreadChat = $this->db->get('chat')->row();
                if(empty($unreadChat)) {
                    $unreadChat = '';
                } else {
                    $unreadChat = 'newChat';
                }
                
                  $data['admin'] .=     '<button class="ca-btn ca-chat-btn d-block '.$unreadChat.'" data-id="'.$admins[$i]['id'].'">'.$admins[$i]['username'].'</button>';
            }
        }
        
            echo json_encode($data);
    }
    

    
}

