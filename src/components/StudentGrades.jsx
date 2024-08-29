import {React, useState, useEffect} from 'react'
import axios from 'axios';
import OneGrade from './OneGrade';

const StudentGrades = () => {
    const [studentGrades, setStudentGrades] = useState(null);

    useEffect(() => {
        axios.get(`http://localhost:8001/api/marks/${window.sessionStorage.getItem("subject_id")}`, {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentGrades(response.data.data)
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            });
    }, []);

    function capitalizeFirstLetter(str) {
        if (typeof str !== 'string' || str.length === 0) {
            return str; // Return the string as is if it's not a string or is empty
        }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
  return (
      <div className='mt-1 px-2'>
          <h2>Student grades in <span className="badge bg-secondary">{capitalizeFirstLetter(window.sessionStorage.getItem("subject_name"))}</span>  </h2>
          <table className="table table-striped table-hover">
              <thead>
                  <tr>
                      <th scope="col">#</th>
                      <th scope="col">Name</th>
                      <th scope="col">Surname</th>
                      <th scope="col">Grade</th>
                  </tr>
              </thead>
              <tbody>
                  {
                      studentGrades == null ? <p>No grades yet</p> : studentGrades.map((studentGrade, index) => (
                          <OneGrade isStudentGrades={true} grade={studentGrade} index={index} key={studentGrade.id} />
                      ))
                  }
              </tbody>
          </table>
      </div>
  )
}

export default StudentGrades