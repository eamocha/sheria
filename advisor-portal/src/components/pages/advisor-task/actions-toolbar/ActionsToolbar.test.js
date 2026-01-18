import React from 'react';
import ReactDOM from 'react-dom';
import ActionsToolbar from './ActionsToolbar';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActionsToolbar />, div);
  ReactDOM.unmountComponentAtNode(div);
});