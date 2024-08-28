import { useState, useEffect } from 'react'
import axios from 'axios';
import OneGrade from './OneGrade';

const Grades = ({user}) => {
    const [grades, setGrades] = useState(null);

    useEffect(() => {
        axios.get('http://localhost:8001/api/marks', {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setGrades(response.data.data)
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            });
    }, []);


  return (
      <div className='mt-1 px-2'>
          <h2><span className="badge bg-secondary"> {user.name}</span> 's grades </h2>
      <table className="table table-striped table-hover">
          <thead>
              <tr>
                  <th scope="col">#</th>
                  <th scope="col">Subject</th>
                  <th scope="col">Grade</th>
              </tr>
          </thead>
          <tbody>
            {
                grades==null ? <p>No grades yet</p> : grades.map((grade, index) => (
                    <OneGrade grade={grade} index={index} key={grade.id}/>
                ))
            }
          </tbody>
      </table>
      </div>
  )
}

export default Grades;