import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePage from './LitigationCasePage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePage />, div);
  ReactDOM.unmountComponentAtNode(div);
});