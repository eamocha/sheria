import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewDetails from './LitigationCasePageBasicViewDetails';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewDetails />, div);
  ReactDOM.unmountComponentAtNode(div);
});