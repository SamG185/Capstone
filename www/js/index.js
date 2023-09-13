document.addEventListener("deviceready", onDeviceReady, false);
var controller;
var BASE_URL;

function onDeviceReady() {
  // Cordova is now initialized. Have fun!

  controller = new AttendanceTaker();

}

function AttendanceTaker() {

  //api link for creating new attendance entry

  var BASE_URL = "http://127.0.0.1/api/";
  var POST_ATTENDANCE_URL = "http://127.0.0.1/api/create/new_attendance.php";

  //check username/password & return credentials (to then move on to the appropriate screen)
  //GOOD
  function login() {
    var user = document.getElementById("user").value;
    var pass = document.getElementById("pass").value;
    var url = BASE_URL + "auth/login.php"
    //everything gets thrown into this onSuccess meaning we need to check in there if an error is thrown, and handle it appropriately.
    function onSuccess(obj) {
      try {
        var result = JSON.parse(obj.responseText.slice(7))

      } catch (error) {
        alert("Please enter a valid username and password. If you have any issues please contact your system administrator at john_doe@hotmail.com")
      }
      var userID = result.id;
      window.localStorage.setItem('ID', userID)
      if (result.category == 'student') {
        var url = document.location.href.slice(0, -10);
        document.location.href = url + "student.html"
        var parentID = findParentID(userID);
        //window.localStorage.setItem('parentID', parentID)
      }
      else if (result.category == 'parent') {
        var url = document.location.href.slice(0, -10);
        document.location.href = url + "parent1.html"
      }
      else if (result.category == 'teacher') {
        var url = document.location.href.slice(0, -10);
        document.location.href = url + "teacher.html"
      }
      else {
        alert("Please enter a valid username and password. If you have any issues please contact your system administrator at john_doe@hotmail.com")
      }
    }
    console.log("order: sending POST to " + url);
    $.ajax(url, { type: "POST", data: { username: user, password: pass }, error: onSuccess });
  }
  //GOOD
  //FUNCTIONS TO MAKE QR SCANNER WORK
  function show() {
    QRScanner.show(function (status) {
      console.log(status);
    });
    var element = document.getElementById("button")
    element.parentNode.removeChild(element);
  }
  //GOOD
  function scan() {
    var callback = function (err, contents) {

      if (err) {
        console.error(err._message);
      }
      //PERFORM DATABASE UPDATING HERE
      alert("Attendance sucessfully logged");
      newAttendance(contents.result);
    };

    QRScanner.scan(callback);
  }

  //GOOD
  //used on student page in conjunction with camera - when QR code scans it fires the class ID into this function
  function newAttendance(id) {
    //comes from the contents of the QR code
    var classID = id.toString();

    //todays date in the appropriate format
    const thedate = new Date();
    let day = thedate.getDate();
    let month = (thedate.getMonth() + 1).toString().padStart(2, "0");
    let year = thedate.getFullYear();

    //This arrangement can be altered based on how we want the date's format to appear.
    let currentDate = `${year}/${month}/${day}`.toString();

    //set the student as present (may be modified by teacher in seperate function)
    var present = "1";

    //find the ID for studentID saved in localstorage when logging in
    var studentID = parseInt(window.localStorage.getItem("ID"));

    //use function to set schoolID in localstorage (this method may need renaming/reusing elsewhere) then assign it to a local variable for readability.
    findSchoolID(studentID)
    var schoolID = window.localStorage.getItem("schoolID");

    function onSuccess(obj) {
      //give visual cue of success here
      if (obj.status == "1") {
      }
    }
    console.log("order: sending POST to " + POST_ATTENDANCE_URL);
    $.ajax(POST_ATTENDANCE_URL, { type: "POST", data: { ClassID: classID, date: currentDate, present: present, schoolID: schoolID, StudentID: studentID }, success: onSuccess });
  }

  //good
  //used to find a list of children given a parent ID - used to dynamically create a list of children div's and update HTML on parent portal
  function loadChildren() {
    //get the ID of the parent (obtained when logging in)
    var parentID = window.localStorage.getItem("ID");
    function onError(obj) {
      var response = obj.responseText.slice(7)

      //parse the sent data ad JSON (necessary due to the weird onError bug)
      var trimmed = JSON.parse(response).data
      if (JSON.parse(response).status == '1') {
        for (var i = 0; i < trimmed.length; i++) {
          if (JSON.stringify(trimmed[i]).length > 0) {
            //modify the json Data into the format we want to display
            var toAdd = JSON.stringify(trimmed[i]).replaceAll("{", "");
            toAdd = toAdd.replaceAll("}", "");
            toAdd = toAdd.replaceAll("}", "");
            toAdd = toAdd.split(',').join('<br>');
            toAdd = toAdd.replace(/["]+/g, ' ');

            //create a new div element and add everything to it
            var newDiv = document.createElement('div');
            newDiv.id = "child" + trimmed[i].ID.toString();
            newDiv.innerHTML = toAdd
            newDiv.className = "child"
            //ad an event listened that determines the child's ID when clicking it, sets it to local storage and then moves on to that students page.
            newDiv.addEventListener('click', function (e) {

              var childID = this.id.slice(-1);
              window.localStorage.setItem("childID", childID);
              var url = document.location.href.slice(0, -12);
              document.location.href = url + "parent2.html"
            })
            //add the new div with all the attendance info to the end of "student_info" which should be the last div in the HTML script.
            document.getElementById("childoverviewmain").appendChild(newDiv);
          }


        }
      }
      else {
        for (var i = 0; i < 4; i++) {
          document.getElementById("child" + i).innerHTML = "";
        }
      }
    }

    var getchildurl = BASE_URL + "read/read_children.php?parentid=" + parentID;
    console.log("order: sending GET to " + getchildurl);
    $.ajax(getchildurl, { type: "GET", data: {}, error: onError });
  }

  //good 
  //used to load the list of students associated with a specific class ID and update a drop down list with those children
  function loadStudentFromClass() {
    //get the value of the currently selected class on the list
    var className = document.getElementById("class_search").value;

    function onError(obj) {
      //clear previous options from the list
      childSelect = document.getElementById("student_search")
      childSelect.innerHTML = "<option value='boop'>Please select a student</option>";
      //load variables
      var child = []
      var children = []

      //start to trim data into useful format
      var response = obj.responseText.slice(7)
      var trimmed = JSON.parse(response).data

      if (JSON.parse(response).status == '1') {
        for (var i = 0; i < trimmed.length; i++) {
          if (JSON.stringify(trimmed[i]).length > 0) {
            //trim data into useful format
            var toAdd = JSON.stringify(trimmed[i]).replaceAll("{", "");
            toAdd = toAdd.replaceAll("}", "");
            toAdd = toAdd.replaceAll("}", "");
            toAdd = toAdd.replace(/["]+/g, ' ');
            toAdd = toAdd.split(',');

            //add each child feature to an array
            child.push(toAdd[0]);
            child.push(toAdd[1]);
            child.push(toAdd[2]);
            //push that child to the array
            children.push(child);
            //reset the child array for next entry
            child = [];
            //document.getElementById("child"+i).innerHTML = toAdd;
          }
          else {
            //document.getElementById("child"+i).innerHTML = "";
          }
        }
      }

      //trim the child data into useable format by id selectors etc

      for (var i = 0; i < children.length; i++) {
        var firstName = children[i][0].slice(13);
        var secondName = children[i][1].slice(13);
        var childID = children[i][2].slice(5);
        var info = firstName + " " + secondName;
        childSelect = document.getElementById("student_search")
        childSelect.options[childSelect.options.length] = new Option(info, childID);
      }
    }

    var getchildurl = BASE_URL + "read/read_student_class.php?classID=" + className;
    console.log("order: sending GET to " + getchildurl);
    $.ajax(getchildurl, { type: "GET", data: {}, error: onError });
  }

  //good
  //used to find student attendance data for both the parent and the teacher portal - puts it into the page after sorting it descending.
  //could update here to allow for multiple "pages" of data, or to filter by month for ease of use.
  function findStudentAttendance() {
    var url = window.location.href.split('/');
    //reusing code so checking to see if I'm on teacher page or parent page and modifying values accordingly.
    if (url[url.length - 1] == "teacher.html") {
      var classID = document.getElementById("class_search").value.trim()
      var childID = document.getElementById("student_search").value.trim()
    }
    else if (url[url.length - 1] == "parent2.html") {
      var childID = window.localStorage.getItem("childID");
      var classID = document.getElementById("class_search").value.trim()
      console.log(childID + " " + classID);
    }



    function onError(obj) {
      var response = obj.responseText.slice(7)
      var trimmed = JSON.parse(response).info
      var toDisplay;
      var containerID = 0

      //remove the existing attendance data showing at the start of calling the function, then add in a blank version ready for attendance data to be appended again later.
      //currently they just get added in in the order of the database
      document.getElementById("student_info").remove();
      var box = document.createElement('div')
      box.id = "student_info"
      document.body.appendChild(box)

      //create list that attendance data will be added to (to allow for date sorting)
      var attendanceData = []

      //add each attendance record to a list of objects (to allow for sorting/sectioning)

      for (var i = 0; i < trimmed.length; i++) {
        containerID++;
        //add the attendance record to list of attendance objects
        //trimmed[i].Date is returning correctly

        var year = trimmed[i].Date.substring(0, 4)
        var month = trimmed[i].Date.substring(5, 7) - 1
        var day = trimmed[i].Date.substring(8, 10)
        attendanceData.push([new Date(year, month, day), trimmed[i].First_name, trimmed[i].Last_name, trimmed[i].Present])
      }


      //sorts using logic that we define 
      var sortedAttendance = attendanceData.slice().sort(
        (objA, objB) => Number(objB[0]) - Number(objA[0]),
      );

      //create a display string that will be loaded onto the page.
      for (var i = 0; i < sortedAttendance.length; i++) {
        toDisplay = "Date: " + sortedAttendance[i][0].toString().substring(0, 15) + "<br>";
        toDisplay = toDisplay + "Name: " + sortedAttendance[i][1];
        toDisplay = toDisplay + " " + sortedAttendance[i][2] + "<br>";
        if (sortedAttendance[i][3] = "1") {
          toDisplay = toDisplay + "Present: Yes <br> <br>";
        }
        else {
          toDisplay = toDisplay + "Present: No <br> <br>"
        }
        //create new div to append as new object - will display all attendance records in individual div elements. 
        var newDiv = document.createElement('div');
        newDiv.id = "container" + containerID.toString();
        newDiv.innerHTML = toDisplay
        newDiv.className = "attendance_result"
        //add the new div with all the attendance info to the end of "student_info" which should be the last div in the HTML script.
        document.getElementById("student_info").appendChild(newDiv);
      }


    }
    //get call to find attendance info using the student ID number and the class ID number.
    var getchildurl = BASE_URL + "read/read_attendance.php?studentid=" + childID + "&classID=" + classID;
    console.log("order: sending GET to " + getchildurl);
    $.ajax(getchildurl, { type: "GET", data: {}, error: onError });
  }

  //GOOD
  //FUNCTIO NTO FIND THE SCHOOLid used in conjunction with new attendance - make a call to database and stores the schoolID in local storage.
  function findSchoolID(studentID) {
    function onError(obj) {

      var response = obj.responseText.slice(7)

      //parse the sent data ad JSON (necessary due to the weird onError bug)
      var trimmed = JSON.parse(response).info

      window.localStorage.setItem("schoolID", trimmed[0].SchoolID)

    }
    var getchildurl = BASE_URL + "read/read_student_info.php?studentid=" + studentID;
    console.log("order: sending GET to " + getchildurl);
    $.ajax(getchildurl, { type: "GET", data: {}, error: onError });
  }

  //functions called by html
  this.show = function () {
    scan();
    show();

  }

  this.newAttendance = function () {
    newAttendance();
  }

  this.loadChildren = function () {
    loadChildren();
  }
  this.loadStudentFromClass = function () {
    loadStudentFromClass();
  }

  this.findStudentAttendance = function () {
    findStudentAttendance();
  }

  this.login = function () {
    login();
  }
}