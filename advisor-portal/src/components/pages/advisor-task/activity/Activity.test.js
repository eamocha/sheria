import React from 'react';
import ReactDOM from 'react-dom';
import Activity from './Activity';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Activity />, div);
  ReactDOM.unmountComponentAtNode(div);
});