import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTaskNoteEditForm from './AdvisorTaskNoteEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTaskNoteEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});