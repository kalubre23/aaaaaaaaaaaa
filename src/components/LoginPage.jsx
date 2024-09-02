import React from "react";
import { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

const LoginPage = ({setRole}) => {
    const [userData, setUserData] = useState({
        username: "",
        password: "",
    });

    let navigate = useNavigate();

    useEffect(() => {
        axios.get('http://localhost:8001/sanctum/csrf-cookie', {
            withCredentials: true, 
        })
            .then(response => {
                console.log(getCookie(document.cookie));
            })
            .catch(error => {
                console.error('Error fetching CSRF token:', error);
            });
    }, []);

    function getCookie(name) {
        var xsrf = name.split("XSRF-TOKEN=")[1];
        return decodeURIComponent(xsrf);
    }

    function handleInput(e) {
        setUserData({
            ...userData,
            [e.target.name]: e.target.value,
        });
    }

    function handleLogin(e) {
        e.preventDefault();
        axios.post('http://localhost:8001/auth/login', {
            "username": userData.username,
            "password": userData.password 
        },
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
                console.log(response.data.user);
                console.log(response.data.child);
                window.sessionStorage.setItem("username", response.data.user.username);
                window.sessionStorage.setItem("name", response.data.user.name);
                window.sessionStorage.setItem("surname", response.data.user.surname);
                window.sessionStorage.setItem("email", response.data.user.email);
                window.sessionStorage.setItem("role", response.data.user.role.name);
                if(response.data.child!=null){
                    window.sessionStorage.setItem("child_username", response.data.child[0].username);
                    window.sessionStorage.setItem("child_name", response.data.child[0].name);
                    window.sessionStorage.setItem("child_surname", response.data.child[0].surname);
                    window.sessionStorage.setItem("child_email", response.data.child[0].email);
                }
                if (response.data.subject != null) {
                    window.sessionStorage.setItem("subject_id", response.data.subject[0].id);
                    window.sessionStorage.setItem("subject_name", response.data.subject[0].name);
                }
                if(window.sessionStorage.getItem("role")==="Teacher"){
                    setRole("Teacher");
                    navigate("/student-grades");
                } else{
                    if (window.sessionStorage.getItem("role") === "Parent"){
                        setRole("Parent");
                    }
                    if (window.sessionStorage.getItem("role") === "Student") {
                        setRole("Student");
                    }
                    navigate("/grades");
                }
                
            })
            .catch(error => {
                console.error('Error in POST request:', error);
            });
        
    }

    return (
        <section
            className="vh-100"
            style={{
                paddingTop: 4.5 + "rem",
            }}
        >
            <div className="container-fluid h-custom">
                <div className="row d-flex justify-content-center align-items-center h-100">
                    <div className="col-md-9 col-lg-6 col-xl-5">
                        <img
                            src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                            className="img-fluid"
                            alt="Sample image"
                        />
                    </div>
                    <div className="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form onSubmit={handleLogin}>
                            <div className="form-outline mb-4">
                                <input
                                    type="text"
                                    id="form3Example3"
                                    className="form-control form-control-lg"
                                    placeholder="Enter your username"
                                    name="username"
                                    onInput={(e) => handleInput(e)}
                                />
                                <label className="form-label" htmlFor="form3Example3">
                                    Username
                                </label>
                            </div>

                            <div className="form-outline mb-3">
                                <input
                                    type="password"
                                    id="form3Example4"
                                    className="form-control form-control-lg"
                                    placeholder="Enter password"
                                    name="password"
                                    onInput={handleInput}
                                />
                                <label className="form-label" htmlFor="form3Example4">
                                    Password
                                </label>
                            </div>

                            <div className="text-center text-lg-start mt-4 pt-2">
                                <button
                                    type="submit"
                                    className="btn btn-primary btn-lg"
                                    style={{
                                        paddingLeft: 2.5 + "rem",
                                        paddingRight: 2.5 + "rem",
                                    }}
                                >
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default LoginPage;