import React from 'react';
import ReactDOM from 'react-dom';
import APStatusBadge from './APStatusBadge';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APStatusBadge />, div);
  ReactDOM.unmountComponentAtNode(div);
});