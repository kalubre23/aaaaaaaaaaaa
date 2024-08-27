import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import LoginPage from './components/LoginPage';
import axios from 'axios';

function App() {
  
  return (
    <BrowserRouter className="App">
      <Routes>
        <Route path='/login' element= { <LoginPage/> }/>
        {/* <Route path='/register' element = { <RegisterPage/> }/>
        <Route path='/' element = { <NavBar token = { token }/> }> 
          <Route path='posts' element = { <PostsPage/> }/>
        </Route> */}
      </Routes>
    </BrowserRouter>
  );
}


export default App;
