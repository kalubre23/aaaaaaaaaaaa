import axios from 'axios';
import {React,useState } from 'react'
import Spinner from './Spinner';

const OneStudent = ({studentMark, setStudentsMarks}) => {
    const [grade, setGrade] = useState();
    const [isLoading, setIsLoading] = useState(false);

    const handleGradeChange = (e) => {
        console.log(e.target.value)
        setGrade(e.target.value);
    };

    let success = null;

    function getCookie(name) {
        var xsrf = name.split("XSRF-TOKEN=")[1];
        return decodeURIComponent(xsrf);
    }

    const handleInputGrade = () => {
        setIsLoading(true);
        //zavrsi poziv od baze
        if(studentMark.mark == null) {
            //post
            axios.post(`http://localhost:8001/api/marks/${window.sessionStorage.getItem("subject_id")}/${studentMark.id}`, {
                "value": grade
            }, {
                headers: {
                    'X-XSRF-TOKEN': getCookie(document.cookie),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                withCredentials: true,
            })
                .then(response => {
                    console.log(response);
                    if (response.message === "Successfully added a mark."){
                        studentMark.mark = grade;
                        setGrade(grade);
                        success = true;
                    }
                })
                .catch(error => {
                    console.error('Error while grading a student!:', error);
                    success=false;
                });
        } else {
            //put
        }
        setIsLoading(false);
    };

  return (
      <div className="card mt-1">
          <div className="card-body">
              <h5 className="card-title">{studentMark.name+" "+studentMark.surname} </h5>
              <h4 className="card-title">Grade: {studentMark.mark == null ? "Not graded" : studentMark.mark}</h4>
              <label className="label mr-1" for="mark">{studentMark.mark == null ? "Grade student" : "Update grade"}</label>
              <div className="d-flex flex-row">
                <input
                    className='form-control pe-2 w-25'
                    id="mark"
                    min="1"
                    max="5" 
                    type="number"
                    placeholder="Enter a number from 1 to 5"
                    onChange={(e) => handleGradeChange(e)}
                    disabled={isLoading}
                />
                <button onClick={handleInputGrade} className="btn btn-primary">
                      {isLoading ? <Spinner/> : (studentMark.mark == null ? "Grade" : "Update")}
                </button>
                  {success === null ? <></> : success === true ? <p className="text-success">Successfully added a grade. </p> : 
                      <p className="text-danger"> Error while grading a student ! </p>}
              </div>
          </div>
      </div>
  )
}

export default OneStudent;