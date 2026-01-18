import React from 'react';
import ReactDOM from 'react-dom';
import APPagination from './APPagination';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPagination />, div);
  ReactDOM.unmountComponentAtNode(div);
});