import {React, useState, useEffect} from 'react'
import axios from 'axios';
import OneStudent from './OneStudent';
import Spinner from './Spinner';

const Students = () => {
    const [studentsMarks, setStudentsMarks] = useState(null);
    const [modalMessage, setModalMessage] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [loading, setLoading] = useState(true);
    const [paginationLinks, setPaginationLinks] = useState([]);

    const subjectId = window.sessionStorage.getItem("subject_id");

    const closeModal = () => {
        setShowModal(false); // Close modal
    };

    useEffect(() => {
        axios.get(`http://localhost:8001/api/students/${subjectId}`, {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentsMarks(response.data.data);
                // setPaginationLinks({
                //     "current_page": response.data.current_page,
                //     "first_page_url": response.data.first_page_url,
                //     "from": response.data.from,
                //     "last_page": response.data.last_page,
                //     "prev_page_url": response.data.prev_page_url,
                //     "links": response.data.links
                // });
                setPaginationLinks(response.data.links);
                console.log(paginationLinks);
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            })
            .finally(() => setLoading(false));;
    }, []);

    function fetchPage(url){
        setLoading(true);
        axios.get(url, {
            withCredentials: true,
        })
            .then(response => {
                console.log(response);
                setStudentsMarks(response.data.data);
                setPaginationLinks(response.data.links);
                console.log(paginationLinks);
            })
            .catch(error => {
                console.error('Error while getting marks!:', error);
            })
            .finally(() => setLoading(false));
    }

    if (loading) {
        return <Spinner />;
    }

    
  return (

    <div>
        <div className='mt-1 px-2'>
          <h2>Students in your course</h2>
          { studentsMarks==null ? "No students in your course" : studentsMarks.map((studentMark) => (
              <OneStudent studentMark={studentMark} setModalMessage={setModalMessage} setShowModal={setShowModal} key={studentMark.id} ></OneStudent>
          )) }
        </div>
          <nav aria-label="Page navigation example">
              <ul className="pagination justify-content-center mt-3">
                  {/* Loop through the pagination links from Laravel response */}
                  {paginationLinks==null ? <></> : paginationLinks.map((link, index) => (
                      <li key={index} className={`page-item ${link.active ? 'active' : ''} ${link.url === null ? 'disabled' : ''}`}>
                          <button
                              className="page-link"
                              href='#' // Use link URL or '#' if null
                              onClick={(e) => {
                                  if (!link.url) e.preventDefault(); // Prevent click if no URL
                                  else fetchPage(link.url); // Call fetchPage function to load new page
                              }}
                              aria-disabled={link.url === null}
                          >
                              {/* Decode HTML entities for labels like "« Previous" and "Next »" */}
                              {link.label === '&laquo; Previous' ? 'Previous' :
                                  link.label === 'Next &raquo;' ? 'Next' : link.label}
                          </button>
                      </li>
                  ))}
              </ul>
          </nav>
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