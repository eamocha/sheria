import React from 'react';
import ReactDOM from 'react-dom';
import ActivityContainer from './ActivityContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});