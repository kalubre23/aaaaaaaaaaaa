import { useState } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import LoginPage from './components/LoginPage';
import NavStudentParent from './components/NavStudentParent';
import Grades from './components/Grades';

function App() {
  const [user, setUser] = useState();

  function returnUser(userData){
    setUser(userData);
  }
  
  return (
    <BrowserRouter className="App">
      <Routes>
        <Route path='/login' element= { <LoginPage returnUser={returnUser}/> }/>
        <Route path='/' element = { <NavStudentParent/> }>
          <Route path='grades' element={ <Grades user={user}/>}></Route>
        </Route>
      </Routes>
    </BrowserRouter>
  );
}


export default App;
