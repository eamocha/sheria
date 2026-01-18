import React from 'react';
import ReactDOM from 'react-dom';
import APRouter from './APRouter';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APRouter />, div);
  ReactDOM.unmountComponentAtNode(div);
});