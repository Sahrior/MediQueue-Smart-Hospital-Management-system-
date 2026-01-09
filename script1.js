document.addEventListener("DOMContentLoaded", function (){


    //--------Home page => Public Display page--------

    const enterbtn = document.getElementById("public_view");

    if(enterbtn){

        enterbtn.addEventListener("click", function(){
            window.location.href = "public_display_system.html";
        });

    }


    //--------Public Display page => Home page--------

    const gobackbtn = document.getElementById("backBtn");

    if(gobackbtn){

        gobackbtn.addEventListener("click", function(){
            window.location.href = "index.html";
        });

    }

    //--------Home page => Patient Login page--------

    const PatinetLoginBtn = document.getElementById("Patient_Login_Button");

    if(PatinetLoginBtn){

        PatinetLoginBtn.addEventListener("click", function(){
            window.location.href = "studentlogin.html"
        })

    }


    //--------Patient Login page => Patient Registration page--------

    const PatientRegistrationBtn = document.getElementById("student_registration_btn")

    if(PatientRegistrationBtn){

        PatientRegistrationBtn.addEventListener("click", function(){
            window.location.href = "studentregistration.html"
        })

    }

    //--------Patient Registration page => Patient Login page--------


    const PatinetLoginBtnFromRegistration = document.getElementById("student_login_btn")

    if(PatinetLoginBtnFromRegistration){

        PatinetLoginBtnFromRegistration.addEventListener("click", function(){
            window.location.href = "studentlogin.html"
        })

    }

    //--------Patient Login page => Patient HOME page--------

    const Patientloginbtntohome = document.getElementById("patient_sign_in")

    if(Patientloginbtntohome){
        Patientloginbtntohome.addEventListener("click", function(){
            window.location.href = "patientportal.html";

        })
    }


    //--------Patient HOME pagee => HOME page--------

    const patientlogoutbtn = document.getElementById("logout_patient")

    if(patientlogoutbtn){
        patientlogoutbtn.addEventListener("click", function(){
            window.location.href = "index.html";
        })
    }


    //--------Patient HOME pagee => Patient ticket page--------

    const standard_appointment = document.getElementById("standard_appointment");

    if(standard_appointment){
        standard_appointment.addEventListener("click", function(){
            window.location.href = "patientticket.html"
        })
    }


    //--------Patient ticket page => Patient HOME pagee--------

    const GoBackTicketToPatientPortal = document.getElementById("GoBackTicketToPatientPortal");

    if(GoBackTicketToPatientPortal){
        GoBackTicketToPatientPortal.addEventListener("click", function(){
            window.location.href = "patientportal.html"
        })
    }



    //--------Patient HOME pagee => Patient emergency ticket page--------

    const emergency_appointment = document.getElementById("emergency_appointment");

    if(emergency_appointment){
        emergency_appointment.addEventListener("click", function(){
            window.location.href = "patientemergencyticket.html"
        })
    }


    //--------Patient ticket page => Patient HOME pagee--------

    const GoBackemergencyTicketToPatientPortal = document.getElementById("GoBackemergencyTicketToPatientPortal");

    if(GoBackemergencyTicketToPatientPortal){
        GoBackemergencyTicketToPatientPortal.addEventListener("click", function(){
            window.location.href = "patientportal.html"
        })
    }



    //--------Home page  => Doctor Login--------

    const DoctorLoginBtn = document.getElementById("doctor_Login_Button");

    if(DoctorLoginBtn){
        DoctorLoginBtn.addEventListener("click", function(){
            window.location.href = "doctorlogin.html"
        })
    }

    //--------Doctor Login  => Doctor sign up--------

    const DoctorRegistrationButton = document.getElementById("doctor_registration_btn");

    if(DoctorRegistrationButton){
        DoctorRegistrationButton.addEventListener("click", function(){
            window.location.href = "doctorsignup.html"
        })
    }


    //--------Doctor sign up  => -------- Doctor Login

    const DoctorLogin = document.getElementById("doctor_login_btn");

    if(DoctorLogin){
        DoctorLogin.addEventListener("click", function(){
            window.location.href = "doctorlogin.html"
        })
    }


    //--------Home page  => Admin enter--------

    const adminenter = document.getElementById("admin_Login_Button");

    if(adminenter){
        adminenter.addEventListener("click", function(){
            window.location.href = "adminenter.html"
        })
    }


    //--------Admin enter  => Admin Dashboard--------

    const adminlogin = document.getElementById("AdminLogin");

    if(adminlogin){
        adminlogin.addEventListener("click", function(){
            window.location.href= "adminhome.html"
        })
    }

    //--------Admin Dashboard  => Home page--------

    const adminlogout = document.getElementById("admin_logout");

    if(adminlogout){
        adminlogout.addEventListener("click", function(){
            window.location.href = "index.html"
        })
    }

    




});