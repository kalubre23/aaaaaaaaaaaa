import {React, useState, useEffect} from 'react'
import axios from 'axios';
import OneStudent from './OneStudent';

const Students = () => {
    const [studentsMarks, setStudentsMarks] = useState(null);
    const [modalMessage, setModalMessage] = useState('');
    const [showModal, setShowModal] = useState(false);

    const closeModal = () => {
        setShowModal(false); // Close modal
    };

    useEffect(() => {
        axios.get(`http://localhost:8001/api/students/${window.sessionStorage.getItem("subject_id")}`, {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentsMarks(response.data);
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            });
    }, []);


  return (

    <div>
        <div className='mt-1 px-2'>
          <h2>Students in your course</h2>
          { studentsMarks==null ? "No students in your course" : studentsMarks.map((studentMark) => (
              <OneStudent studentMark={studentMark} setModalMessage={setModalMessage} setShowModal={setShowModal} key={studentMark.id} ></OneStudent>
          )) }
        </div>
        {/* Modal */}
        {showModal ? (
            <div style={modalStyle}>
                <div style={modalContentStyle}>
                    <p>{modalMessage}</p>
                    <button className='btn btn-primary' onClick={closeModal}>Close</button>
                </div>
            </div>
        ) : <></>}
    </div>
  )
}

const modalStyle = {
    position: 'fixed',
    top: '0',
    left: '0',
    width: '100%',
    height: '100%',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
};

const modalContentStyle = {
    backgroundColor: 'white',
    padding: '20px',
    borderRadius: '5px',
    textAlign: 'center',
};

export default Students;