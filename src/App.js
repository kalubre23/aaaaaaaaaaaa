import { useState } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import LoginPage from './components/LoginPage';
import NavStudentParent from './components/NavStudentParent';
import Grades from './components/Grades';
import ProfilePage from './components/ProfilePage';

function App() {
  // const [user, setUser] = useState();

  // function returnUser(userData){
  //   setUser(userData);
  // }
  
  return (
    <BrowserRouter className="App">
      <Routes>
        <Route path='/login' element= { <LoginPage /> }/>
        <Route path='/' element = { <NavStudentParent/> }>
          <Route path='grades' element={ <Grades /> }/>
          <Route path='profile' element= { <ProfilePage/> }/>
        </Route>
      </Routes>
    </BrowserRouter>
  );
}


export default App;
