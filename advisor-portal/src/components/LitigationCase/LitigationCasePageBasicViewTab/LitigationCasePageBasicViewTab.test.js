import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewTab from './LitigationCasePageBasicViewTab';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewTab />, div);
  ReactDOM.unmountComponentAtNode(div);
});