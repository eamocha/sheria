import React from 'react';
import ReactDOM from 'react-dom';
import AddFileForm from './AddFileForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AddFileForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});