import React from 'react';
import ReactDOM from 'react-dom';
import ActivityDetailsRow from './ActivityDetailsRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityDetailsRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});