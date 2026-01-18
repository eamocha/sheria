import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageBadge from './LitigationCaseStageBadge';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageBadge />, div);
  ReactDOM.unmountComponentAtNode(div);
});