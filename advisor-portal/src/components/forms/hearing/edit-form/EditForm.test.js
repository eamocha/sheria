import React from 'react';
import ReactDOM from 'react-dom';
import EditForm from './EditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<EditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});