import React from 'react';
import ReactDOM from 'react-dom';
import EditFolderForm from './EditFolderForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<EditFolderForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});