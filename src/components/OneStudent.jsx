import axios from 'axios';
import {React,useState, useRef } from 'react'
import Spinner from './Spinner';

const OneStudent = ({ studentMark, setModalMessage, setShowModal }) => {
    const [grade, setGrade] = useState(studentMark.mark);
    
    const inputRef = useRef(null);

    function getCookie(name) {
        var xsrf = name.split("XSRF-TOKEN=")[1];
        return decodeURIComponent(xsrf);
    }

    

    const handleInputGrade = () => {
        let inputGrade = inputRef.current.value;
        //zavrsi poziv od baze
        if(studentMark.mark == null) {
            //post
            axios.post(`http://localhost:8001/api/marks/${window.sessionStorage.getItem("subject_id")}/${studentMark.user_id}`, {
                "value": inputGrade
            }, {
                headers: {
                    'X-XSRF-TOKEN': getCookie(document.cookie),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                withCredentials: true,
            })
                .then(response => {
                    inputRef.current.value = "";
                    console.log(response);
                    studentMark.mark = inputGrade;
                    setGrade(inputGrade);
                    setModalMessage('Succesfully added a grade!');
                    setShowModal(true);
                
                })
                .catch(error => {
                    console.error('Error while grading a student!:', error);
                    setModalMessage(error.message);
                    setShowModal(true);
                });
        } else {
            //put
            axios.put(`http://localhost:8001/api/marks/${studentMark.mark_id}`, {
                "value": inputGrade
            }, {
                headers: {
                    'X-XSRF-TOKEN': getCookie(document.cookie),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                withCredentials: true,
            })
                .then(response => {
                    inputRef.current.value = "";
                    console.log(response);
                    studentMark.mark = inputGrade;
                    setGrade(inputGrade);
                    setModalMessage('Succesfully updated a grade!');
                    setShowModal(true);

                })
                .catch(error => {
                    console.error('Error while updating a grade!:', error);
                    setModalMessage(error.message);
                    setShowModal(true);
                });
        }
    };

    const handleDeleteGrade = () => { 
        axios.delete(`http://localhost:8001/api/marks/${studentMark.mark_id}`, 
                {
                headers: {
                    'X-XSRF-TOKEN': getCookie(document.cookie),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                withCredentials: true,
            })
                .then(response => {
                    inputRef.current.value = "";
                    console.log(response);
                    studentMark.mark = null;
                    setGrade(null);
                    setModalMessage('Succesfully deleted grade!');
                    setShowModal(true);
                })
                .catch(error => {
                    console.error('Error while deleting grade !: ', error);
                    setModalMessage("Error while deleting grade!\n", error.message);
                    setShowModal(true);
                });
     }

  return (
      <div className="card mt-1">
          <div className="card-body">
              <h5 className="card-title">{studentMark.name+" "+studentMark.surname} </h5>
              <h4 className="card-title">Grade: {grade == null ? "Not graded" : grade}</h4>
              <label className="label mr-1" for="mark">{grade == null ? "Grade student" : "Update grade"}</label>
              <div className="d-flex flex-row">
                <input
                    className='form-control pe-2 w-25'
                    id="mark"
                    min="1"
                    max="5" 
                    type="number"
                    placeholder="Enter a number from 1 to 5"
                    ref={inputRef}
                />
                <button onClick={handleInputGrade} className="btn btn-primary ms-1">
                      {grade == null ? "Grade" : "Update"}
                </button>
                  {grade == null ? <></> : 
                  <button onClick={handleDeleteGrade} className="btn btn-danger ms-1">
                      Delete grade
                  </button>}
              </div>
          </div>
            
      </div>
  )
}



export default OneStudent;