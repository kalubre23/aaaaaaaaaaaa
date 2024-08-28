import React from 'react'

const NavStudentParent = ({role}) => {
  return (
      <ul className="nav justify-content-between" style={{ paddingTop: "20px", paddingBottom: "20px", borderBottom: "2px solid #c1c1bf", backgroundColor: "#953eec", fontSize: "1.25rem" }}>
          <li className="nav-item" style={{
              color: "#FFFFFF",
              fontWeight: "bold",           /* Bold text */
              fontSize: "1.5rem",           /* Increase font size */
              letterSpacing: "1px",         /* Add some space between letters */
              textTransform: "uppercase",   /* Convert text to uppercase */
              textShadow: "2px 2px 4px rgba(0, 0, 0, 0.5)", /* Add a subtle shadow */
          }}>
              <span className="nav-link" style={{ color: "#FFFFFF", fontWeight: "bold" }}>E-dnevnik</span>
          </li>
        <ul className="nav justify-content-end">
              <li className="nav-item" style={{ marginRight: "15px" }}>
                  <a className="nav-link active" style={{ color: "#FFFFFF"}} aria-current="page" href="#">My grades</a>
            </li>
              
              <li className="nav-item" style={{ marginRight: "15px"}}>
                  <a className="nav-link" href="#" style={{ color: "#FFFFFF"}}>Profile</a>
            </li>
        </ul>
      </ul>
  )
}

export default NavStudentParent;