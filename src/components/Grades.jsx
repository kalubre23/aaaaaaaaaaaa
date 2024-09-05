import { React, useState, useEffect } from 'react'
import axios from 'axios';
import OneGrade from './OneGrade';
import Spinner from './Spinner';

const Grades = ({isTeacher}) => {
    const [studentGrades, setStudentGrades] = useState(null);
    const [loading, setLoading] = useState(true);

    const url = `http://localhost:8001/api/marks/${isTeacher && window.sessionStorage.getItem("subject_id") ? window.sessionStorage.getItem("subject_id") : ""}`;
    useEffect(() => {
        axios.get(url, {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentGrades(response.data.data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
                setLoading(false);
            });
    }, []);

    function capitalizeFirstLetter(str) {
        if (typeof str !== 'string' || str.length === 0) {
            return str;
        }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    if (loading) {
        return <Spinner/>;
    }

    return (
        <div className='mt-1 px-2'>
            {isTeacher ? 
                <h2>Student grades in <span className="badge bg-secondary">{capitalizeFirstLetter(window.sessionStorage.getItem("subject_name"))}</span>  </h2> 
                :
                <h2><span className="badge bg-secondary"> {window.sessionStorage.getItem("role") === "Parent" ? window.sessionStorage.getItem("child_name") : window.sessionStorage.getItem("name")} </span> 's grades </h2>   
            }
        
            <table className="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        {isTeacher ? <>
                            <th scope="col">Name</th>
                            <th scope="col">Surname</th> </> : 
                            <th scope="col">Subject</th>}
                        <th scope="col">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        studentGrades == null ? <p>No grades yet</p> : studentGrades.map((studentGrade, index) => (
                            <OneGrade isTeacher={isTeacher} grade={studentGrade} index={index} key={studentGrade.id} />
                        ))
                    }
                </tbody>
            </table>
        </div>
    )
}

export default Grades;