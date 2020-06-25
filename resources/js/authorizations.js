let user = window.App.user;

module.exports = {

    owns (model, prop = 'user_id') {
        console.log('im here' + model[prop]);
        return model[prop] === user.id;
    },

    isAdmin () {
        return ['JohnDoe', 'sf'].includes(user.name);
    }
};