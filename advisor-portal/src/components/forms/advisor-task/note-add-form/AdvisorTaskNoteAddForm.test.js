import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTaskNoteAddForm from './AdvisorTaskNoteAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTaskNoteAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});