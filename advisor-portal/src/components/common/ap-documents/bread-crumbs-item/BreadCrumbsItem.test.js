import React from 'react';
import ReactDOM from 'react-dom';
import BreadCrumbsItem from './BreadCrumbsItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<BreadCrumbsItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});