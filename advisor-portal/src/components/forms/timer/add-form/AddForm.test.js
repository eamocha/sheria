import React from 'react';
import ReactDOM from 'react-dom';
import AddForm from './AddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});