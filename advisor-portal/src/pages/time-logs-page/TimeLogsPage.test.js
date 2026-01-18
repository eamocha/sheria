import React from 'react';
import ReactDOM from 'react-dom';
import TimeLogsPage from './TimeLogsPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TimeLogsPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});