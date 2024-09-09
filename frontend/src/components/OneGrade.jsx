import React from 'react'

const OneGrade = ({isTeacher, grade, index}) => {
  return (
    <>
    {
        isTeacher ?
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