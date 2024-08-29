import {React, useEffect} from 'react'
import { Outlet, useNavigate } from 'react-router-dom'
import axios from 'axios';

const NavTeacher = ({setRole}) => {
    

    let navigate = useNavigate();

    useEffect(() => {
        if (window.sessionStorage.length === 0) {
            setRole(null);
            navigate('/login');
        }
    }, []);

    function getCookie(name) {
        var xsrf = name.split("XSRF-TOKEN=")[1];
        return decodeURIComponent(xsrf);
    }

    const handleLogout = () => {
        axios.post('http://localhost:8001/auth/logout', {},
            {
                headers: {
                    'X-XSRF-TOKEN': getCookie(document.cookie),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                withCredentials: true
            }
        )
            .then(response => {
                window.sessionStorage.clear();
                window.localStorage.clear();
                setRole(null);
                navigate("/login");
            })
            .catch(error => {
                console.error('Error during logout:', error);
            });
    };
  return (
    <div>
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
                      <a className="nav-link active" style={{ color: "#FFFFFF" }} href="/student-grades" aria-current="page">All grades</a>
                  </li>

                  <li className="nav-item" style={{ marginRight: "15px" }}>
                      <a className="nav-link" href="/profile" style={{ color: "#FFFFFF" }}>Grade a student</a>
                  </li>
                  <li className="nav-item" style={{ marginRight: "15px" }}>
                      <a className="nav-link" href="/profile" style={{ color: "#FFFFFF" }}>Profile</a>
                  </li>
                  <li className="nav-item" style={{ marginRight: "15px" }}>
                      <button className="nav-link" onClick={handleLogout} style={{ color: "#adabac" }}>Logout</button>
                  </li>
              </ul>
          </ul>
          <Outlet />
    </div>
  )
}

export default NavTeacher;