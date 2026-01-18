import React from 'react';
import ReactDOM from 'react-dom';
import APLegalCaseNoteAddForm from './APLegalCaseNoteAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APLegalCaseNoteAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});