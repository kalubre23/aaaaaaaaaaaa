import React from 'react'

const OneGrade = ({grade, index}) => {
  return (
      <tr>
          <th scope="row">{index + 1}</th>
          <td>{grade.subject.name}</td>
          <td>{grade.value}</td>
      </tr>
  )
}

export default OneGrade;