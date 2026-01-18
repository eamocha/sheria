import React from 'react';
import ReactDOM from 'react-dom';
import EditFileForm from './EditFileForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<EditFileForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});