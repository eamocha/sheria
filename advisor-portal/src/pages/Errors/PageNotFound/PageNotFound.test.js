import React from 'react';
import ReactDOM from 'react-dom';
import PageNotFound from './PageNotFound';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<PageNotFound />, div);
  ReactDOM.unmountComponentAtNode(div);
});