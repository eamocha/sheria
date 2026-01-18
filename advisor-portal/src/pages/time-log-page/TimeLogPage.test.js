import React from 'react';
import ReactDOM from 'react-dom';
import TimeLogPage from './TimeLogPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TimeLogPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});