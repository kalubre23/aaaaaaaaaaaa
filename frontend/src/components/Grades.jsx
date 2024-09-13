import { React, useState, useEffect } from 'react'
import axios from 'axios';
import OneGrade from './OneGrade';
import Spinner from './Spinner';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

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

    const exportToPDF = () => {
        const studentName = window.sessionStorage.getItem("role") === "Parent"
            ? window.sessionStorage.getItem("child_name")
            : window.sessionStorage.getItem("name");
        
        const input = document.getElementById('table-to-pdf'); // ID of the table container

        html2canvas(input).then((canvas) => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF();
            const imgWidth = 210; // A4 width in mm
            const pageHeight = 295; // A4 height in mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let position = 40; //nakon teksta

            // Add student name to the top of the PDF
            pdf.setFontSize(16);
            pdf.text(`${studentName}'s grades:`, 10, 20);

            console.log("PDF object before adding image:", pdf);

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            let heightLeft = imgHeight - pageHeight;


            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save(`${studentName}'s-grades.pdf`);
        });
    };

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
            

            <div id='table-to-pdf'>
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
            <button onClick={exportToPDF} className='btn btn-primary mb-2'>
                Export to PDF
            </button>
        </div>
    )
}

export default Grades;