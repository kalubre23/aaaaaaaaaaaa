import { useState } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import LoginPage from './components/LoginPage';
import NavStudentParent from './components/NavStudentParent';

function App() {
  const [role, setRole] = useState();

  function returnRole(rolee){
    console.log(rolee);
    setRole(rolee);
  }
  
  return (
    <BrowserRouter className="App">
      <Routes>
        <Route path='/login' element= { <LoginPage returnRole={returnRole}/> }/>
        <Route path='/' element = { <NavStudentParent role={role}/> }/>
      </Routes>
    </BrowserRouter>
  );
}


export default App;
