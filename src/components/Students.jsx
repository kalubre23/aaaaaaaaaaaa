import {React, useState, useEffect} from 'react'
import axios from 'axios';
import OneStudent from './OneStudent';

const Students = () => {
    const [studentsMarks, setStudentsMarks] = useState(null);

    useEffect(() => {
        axios.get('http://localhost:8001/api/students', {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentsMarks(response.data);
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            });
    }, []);


  return (
      <div className='mt-1 px-2'>
        <h2>Students in your course</h2>
        { studentsMarks==null ? "No students in your course" : studentsMarks.map((studentMark) => (
            <OneStudent studentMark={studentMark} key={studentMark.id} setStudentsMarks={setStudentsMarks}></OneStudent>
        )) }
    </div>
  )
}

export default Students;