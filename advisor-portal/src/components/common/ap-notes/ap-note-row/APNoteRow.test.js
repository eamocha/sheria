import React from 'react';
import ReactDOM from 'react-dom';
import APNoteRow from './APNoteRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APNoteRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});