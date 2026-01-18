import React from 'react';
import ReactDOM from 'react-dom';
import DialogHeader from './DialogHeader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<DialogHeader />, div);
  ReactDOM.unmountComponentAtNode(div);
});