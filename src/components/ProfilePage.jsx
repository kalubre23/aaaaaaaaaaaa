import React from 'react'

const ProfilePage = () => {
  return ( 
    <div>
        <table className="table table-striped">
            <tbody>
                <tr>
                    <th scope="row">Name:</th>
                    <td>{window.sessionStorage.getItem("name")}</td>
                </tr>
                <tr>
                    <th scope="row">Surname:</th>
                    <td>{window.sessionStorage.getItem("surname")}</td>
                </tr>
                  <tr>
                    <th scope="row">Username:</th>
                    <td>{window.sessionStorage.getItem("username")}</td>
                  </tr>
                  <tr>
                    <th scope="row">Email:</th>
                    <td>{window.sessionStorage.getItem("email")}</td>
                  </tr>
                  <tr>
                    <th scope="row">Role:</th>
                    <td>{window.sessionStorage.getItem("role")}</td>
                  </tr>
                  {
                    window.sessionStorage.getItem("role")==="Parent" ? 
                        (<>
                        <tr>
                            <th scope="row">Child's Name:</th>
                            <td>{window.sessionStorage.getItem("child_name")}</td>
                        </tr>
                        <tr>
                            <th scope="row">Child's Surname:</th>
                                  <td>{window.sessionStorage.getItem("child_surname")}</td>
                        </tr>
                        <tr>
                            <th scope="row">Child's Username:</th>
                            <td>{window.sessionStorage.getItem("child_username")}</td>
                        </tr>
                        <tr>
                            <th scope="row">Child's Email:</th>
                            <td>{window.sessionStorage.getItem("child_email")}</td>
                        </tr>
                        <tr>
                            <th scope="row">Child's Role:</th>
                            <td>Student</td>
                        </tr> </>) : window.sessionStorage.getItem("role") === "Teacher" ? (
                          <>
                          <tr>
                            <th scope="row">Subject:</th>
                            <td>{window.sessionStorage.getItem("subject_name")}</td>
                          </tr>
                          <tr>
                            <th scope="row">Taught by:</th>
                            <td>{window.sessionStorage.getItem("name") + " " + window.sessionStorage.getItem("surname")}</td>
                          </tr>
                        </>) : <></>
                }
                </tbody>
          </table>
    </div>
  )
}

export default ProfilePage;