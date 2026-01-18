import React from 'react';
import ReactDOM from 'react-dom';
import BreadCrumbsContainer from './BreadCrumbsContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<BreadCrumbsContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});