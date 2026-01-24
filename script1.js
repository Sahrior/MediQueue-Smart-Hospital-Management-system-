document.addEventListener("DOMContentLoaded", function () {
    
    //--------Home page => Public Display page--------
    const enterbtn = document.getElementById("public_view");
    if (enterbtn) {
        enterbtn.addEventListener("click", function () {
            window.location.href = "public_display_system.php";
        });
    }

    //--------Public Display page => Home page--------
    const gobackbtn = document.getElementById("backBtn");
    if (gobackbtn) {
        gobackbtn.addEventListener("click", function () {
            window.location.href = "index.html";
        });
    }

    //--------Home page => Patient Login page--------
    const PatinetLoginBtn = document.getElementById("Patient_Login_Button");
    if (PatinetLoginBtn) {
        PatinetLoginBtn.addEventListener("click", function () {
            window.location.href = "studentlogin.html";
        });
    }

    //--------Patient Login page => Patient Registration page--------
    const PatientRegistrationBtn = document.getElementById("student_registration_btn");
    if (PatientRegistrationBtn) {
        PatientRegistrationBtn.addEventListener("click", function () {
            window.location.href = "studentregistration.html";
        });
    }

    //--------Patient Registration page => Patient Login page--------
    const PatinetLoginBtnFromRegistration = document.getElementById("student_login_btn");
    if (PatinetLoginBtnFromRegistration) {
        PatinetLoginBtnFromRegistration.addEventListener("click", function () {
            window.location.href = "studentlogin.html";
        });
    }

    // REMOVED: Patientloginbtntohome (Let the PHP form handle the login)
    // REMOVED: patientlogoutbtn (Let the PHP logout script handle this)

    /* --- CRITICAL UPDATE ---
       REMOVED: standard_appointment listener
       Why: This allows your PHP "onclick=openModal()" to work without being blocked.
    */

    //--------Patient ticket page => Patient HOME page--------
    const GoBackTicketToPatientPortal = document.getElementById("GoBackTicketToPatientPortal");
    if (GoBackTicketToPatientPortal) {
        GoBackTicketToPatientPortal.addEventListener("click", function () {
            // UPDATED: Now points to the PHP portal
            window.location.href = "patientportal.php";
        });
    }

    /* --- CRITICAL UPDATE ---
       REMOVED: emergency_appointment listener
       Why: This allows your PHP form to submit directly to the database.
    */

    //--------Patient emergency ticket page => Patient HOME page--------
    const GoBackemergencyTicketToPatientPortal = document.getElementById("GoBackemergencyTicketToPatientPortal");
    if (GoBackemergencyTicketToPatientPortal) {
        GoBackemergencyTicketToPatientPortal.addEventListener("click", function () {
            // UPDATED: Now points to the PHP portal
            window.location.href = "patientportal.php";
        });
    }

    //--------Home page => Doctor Login--------
    const DoctorLoginBtn = document.getElementById("doctor_Login_Button");
    if (DoctorLoginBtn) {
        DoctorLoginBtn.addEventListener("click", function () {
            window.location.href = "doctorlogin.html";
        });
    }

    //--------Doctor Login => Doctor sign up--------
    const DoctorRegistrationButton = document.getElementById("doctor_registration_btn");
    if (DoctorRegistrationButton) {
        DoctorRegistrationButton.addEventListener("click", function () {
            window.location.href = "doctorsignup.html";
        });
    }

    //--------Doctor sign up => Doctor Login--------
    const DoctorLogin = document.getElementById("doctor_login_btn");
    if (DoctorLogin) {
        DoctorLogin.addEventListener("click", function () {
            window.location.href = "doctorlogin.html";
        });
    }

    //--------Home page => Admin enter--------
    const adminenter = document.getElementById("admin_Login_Button");
    if (adminenter) {
        adminenter.addEventListener("click", function () {
            window.location.href = "adminenter.html";
        });
    }
    
});