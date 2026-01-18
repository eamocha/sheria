import React from 'react';
import ReactDOM from 'react-dom';
import Notes from './Notes';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Notes />, div);
  ReactDOM.unmountComponentAtNode(div);
});