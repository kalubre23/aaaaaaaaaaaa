import React from 'react'

const OneGrade = ({isStudentGrades, grade, index}) => {
  return (
    <>
    {
      isStudentGrades ?
      (<tr>
        <th scope="row">{index + 1}</th>
        <td>{grade.student.name}</td>
        <td>{grade.student.surname}</td>
        <td>{grade.value}</td>
      </tr>) :
        (<tr>
          <th scope="row">{index + 1}</th>
          <td>{grade.subject.name}</td>
          <td>{grade.value}</td>
        </tr>)
    } 
    </>
  )
}

export default OneGrade;