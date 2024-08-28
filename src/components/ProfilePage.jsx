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
            </tbody>
        </table>
    </div>
  )
}

export default ProfilePage;