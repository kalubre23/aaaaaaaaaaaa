import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import LoginPage from './components/LoginPage';
import NavStudentParent from './components/NavStudentParent';
import Grades from './components/Grades';
import ProfilePage from './components/ProfilePage';
import NavTeacher from './components/NavTeacher';
import Subject from './components/Subject';

function App() {
  // const [user, setUser] = useState();

  // function returnUser(userData){
  //   setUser(userData);
  // }
  const [role, setRole] = useState(() => window.sessionStorage.getItem('role'));

  useEffect(() => {
    if (role) {
      // Store role in sessionStorage whenever it changes
      window.sessionStorage.setItem('role', role);
    }
  }, [role]);

  // If role is not loaded yet, you can show a loading state or return null
  // if (role === null) {
  //   return <div>Loading...</div>;
  // }

  
  // return (
  //   <BrowserRouter className="App">
  //     <Routes>
  //       <Route path='/login' element= { <LoginPage /> }/>
  //       <Route path='/' element = { window.sessionStorage.getItem("role")==="Teacher" ? <NavTeacher/> :<NavStudentParent/> }>
  //         {
  //           window.sessionStorage.getItem("role")==="Teacher" ? <Route path='/subject' element={ <Subject/> }/> :
  //         <Route path='grades' element={ <Grades /> }/>
  //         }
  //         <Route path='profile' element= { <ProfilePage/> }/>
  //       </Route>
  //     </Routes>
  //   </BrowserRouter>
  // );

  // return (
  //   <BrowserRouter>
  //     <Routes>
  //       {/* Login Route */}
  //       <Route path='/login' element={<LoginPage setRole={setRole}/>} />

  //       {/* Conditional Rendering Based on Role */}
  //       {role === "Teacher" ? (
  //         <Route path='/' element={<NavTeacher />}>
  //           <Route path='subject' element={<Subject />} />
  //           <Route path='profile' element={<ProfilePage />} />
  //           <Route path='*' element={<Navigate to="/subject" />} /> {/* Redirect to a default route */}
  //         </Route>
  //       ) : (
  //         <Route path='/' element={<NavStudentParent />}>
  //           <Route path='grades' element={<Grades />} />
  //           <Route path='profile' element={<ProfilePage />} />
  //           <Route path='*' element={<Navigate to="/grades" />} /> {/* Redirect to a default route */}
  //         </Route>
  //       )}
        
  //       {/* Redirect undefined paths to login or other routes */}
  //       <Route path="*" element={<Navigate to="/login" />} />
  //     </Routes>
  //   </BrowserRouter>
  // );

  return (
    <BrowserRouter>
      <Routes>
        {!role ? (
          <>
            {/* If no role is set, show the login page */}
            <Route path="/login" element={<LoginPage setRole={setRole} />} />
            <Route path="*" element={<Navigate to="/login" />} />
          </>
        ) : role === 'Teacher' ? (
          <>
            {/* Teacher routes */}
            <Route path="/" element={<NavTeacher />}>
              <Route path="subject" element={<Subject />} />
              <Route path="profile" element={<ProfilePage />} />
              <Route path="*" element={<Navigate to="/profile" />} />
            </Route>
          </>
        ) : (
          <>
            {/* Student/Parent routes */}
            <Route path="/" element={<NavStudentParent setRole={setRole}/>}>
              <Route path="grades" element={<Grades />} />
              <Route path="profile" element={<ProfilePage />} />
              <Route path="*" element={<Navigate to="/profile" />} />
            </Route>
          </>
        )}
      </Routes>
    </BrowserRouter>
  );

}


export default App;
