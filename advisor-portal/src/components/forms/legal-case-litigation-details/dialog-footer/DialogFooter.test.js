import React from 'react';
import ReactDOM from 'react-dom';
import DialogFooter from './DialogFooter';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<DialogFooter />, div);
  ReactDOM.unmountComponentAtNode(div);
});