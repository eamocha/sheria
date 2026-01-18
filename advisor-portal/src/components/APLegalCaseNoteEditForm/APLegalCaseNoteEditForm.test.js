import React from 'react';
import ReactDOM from 'react-dom';
import APLegalCaseNoteEditForm from './APLegalCaseNoteEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APLegalCaseNoteEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});